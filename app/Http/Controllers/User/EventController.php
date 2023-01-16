<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amtanesia;
use App\Models\Event;
use App\Models\History;
use App\Models\HistoryDetail;
use App\Models\Periode;
use App\Models\User;
use ArrayObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class EventController extends Controller
{
    /*
        Berisi fungsi untuk: menampilkan data event di mobile.
        API dipanggil di proyek mobile: RestClient.cs
            View: EventAct.xaml, DetailEventAct.xaml
            View Model: EventViewModel.cs,  DetailEventViewModel.cs
    */
    public function __construct()
    {
        $this->amtanesia = new Amtanesia();
        $this->user = new User();
        $this->event = new Event();
        $this->periode = new Periode();
        $this->history = new History();
        $this->historyDetail = new HistoryDetail();
    }

    public function getAllEvents(Request $request)
    {
        $eventData = $this->getAllTheEvents("all", $request); //dari fungsi getAllTheEvents controller ini.
        echo json_encode($eventData);
    }

    public function getAllOtherEvents(Request $request)
    {
        $eventData = $this->getAllTheEvents("other", $request); //dari fungsi getAllTheEvents controller ini.
        echo json_encode($eventData);
    }

    public function getAllTheEvents($key, Request $request)
    {
         /** 
         * Fungsi: Mendapat data event berdasarkan tipe event di mobile.
         * Param:
         *      - request:
         *             - api_key    -> API key yang ada di parameter URL 
         * Return:
         *      - events      -> Data daftar event.
        */
        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);
        $events = new ArrayObject();
        if ($resultApiKey['status']) {
            $resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);
            if ($key == "all") {   // Menu Event - All pada mobile
                $resultEvents = $this->event->getEvents(1, $this->periode->getActivePeriode(), $resultProfile->TYPE); //dari Model Event. Mendapat data event dengan tipe Event 1 (Event Challenge) pada periode aktif dan tipe profile tertentu (dosen/mahasiswa)
            } else if ($key == "other") { // Menu Event - Others pada mobile
                $resultEvents = $this->event->getEvents(2, $this->periode->getActivePeriode(), $resultProfile->TYPE); //dari Model Event. Mendapat data event dengan tipe Event 2 (IT Lounge Challenge) pada periode aktif dan tipe profile tertentu (dosen/mahasiswa)
            }
            //return $resultEvents;

            if (count($resultEvents) <= 0) {
                $events['status'] = true;
                $events['message'] = 'Get all events success!';
                $events['events'] = [];
            } else {
                $temp = array();

                $events['status'] = true;
                $events['message'] = 'Get all events success!';

                $eventItem = array();
                $j = -1;
                $k = 0;
                //Memasukkan data ke Array
                for ($i = 0; $i < count($resultEvents); $i++) {
                    if (!in_array($resultEvents[$i]->ID_EVENTS, $temp)) {
                        array_push($temp, $resultEvents[$i]->ID_EVENTS);
                        $j++;
                        $k = 0;

                        $eventRoleItem['role_id'] = $resultEvents[$i]->ID_EVENT_ROLE;
                        $eventRoleItem['role_name'] = $resultEvents[$i]->NAME_EVENT_ROLE;
                        $eventRoleItem['role_info'] = $resultEvents[$i]->ROLE_INFO;
                        $eventRoleItem['point'] = $resultEvents[$i]->POINT;

                        $eventItem['event_id'] = $resultEvents[$i]->ID_EVENTS;
                        $eventItem['event_title'] = $resultEvents[$i]->NAME_EVENTS;
                        $eventItem['event_info'] = $resultEvents[$i]->DESC_EVENTS;
                        $eventItem['event_date_start'] = $resultEvents[$i]->DATE_START;
                        $eventItem['event_date_end'] = $resultEvents[$i]->DATE_FINISH;
                        $eventItem['event_role'] = array($eventRoleItem);

                        $photo['small_url'] = $resultEvents[$i]->PHOTO_SMALL;
                        $photo['medium_url'] = $resultEvents[$i]->PHOTO_MEDIUM;
                        $photo['large_url'] = $resultEvents[$i]->PHOTO_LARGE;

                        $eventItem['event_photo'] = $photo;

                        $events['events'][] = $eventItem;

                        $k++;
                    } else {
                        $eventRoleItem['role_id'] = $resultEvents[$i]->ID_EVENT_ROLE;
                        $eventRoleItem['role_name'] = $resultEvents[$i]->NAME_EVENT_ROLE;
                        $eventRoleItem['role_info'] = $resultEvents[$i]->ROLE_INFO;
                        $eventRoleItem['point'] = $resultEvents[$i]->POINT;
                        $events['events'][$j]['event_role'][$k] = $eventRoleItem;
                        $k++;
                    }
                }
            }
        } else {
            $events['status'] = false;
            $events['message'] = 'Api Key is not valid!';
        }
        // echo json_encode($events);
        return $events;
    }

    public function joinEvent(Request $request)
    { 
         /** 
         * Fungsi: Menambah data riwayat keikutsertaan event user tertentu dan memperbaharui poin user tersebut.
         * Param:
         *      - request:
         *             - api_key    -> API key yang ada di parameter URL 
         *             - event_id   -> ID event yang ada di tabel events
         *             - event_role_id -> ID event role (photo, fb, twitter)
         *             - event_role_info -> hashtag yang digunakan
         * Return:
         *      - data -> Array:
         *             - status     -> Status API Key valid atau tidak
         *             - message    -> Pesan pemanggilam API
         *             - point_earned -> Poin yang didapat  
        */
        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);
        if ($resultApiKey['status']) {
            $storeData = $request->all();

            $validate = Validator::make($storeData, [
                'event_id' => 'required',
                'event_role_id' => 'required',
                'event_role_info' => 'required',
            ]); //membuat rule validasi input

            if ($validate->fails()) {
                $data['status'] = false;
                $data['message'] = 'Data input problem!';
                return response([
                    'status' => false,
                    'message' => $validate->errors(),
                ], 400); //return error invalid input
            }

            //Get data user
            $resultProfile          = $this->user->getProfileStudent($resultApiKey['device_id']); //dari Mdoel User. Untuk mendapat profil pengguna
            $resultUserSocial       = $this->user->getUserSocial($resultProfile->ID_USERS); // dari Model User. Untuk

            //Mengatur data untuk masuk ke array
            $recentPoint            = $resultProfile->POINTS;
            $userId                 = $resultProfile->ID_USERS;

            $eventId                = $storeData['event_id'];

            $arr_event_role_id      = $storeData['event_role_id'];
            $eventRoleIdS           = explode(',', $arr_event_role_id);

            $arr_event_role_info    = $storeData['event_role_info'];
            $eventRoleInfoS         = explode(',', $arr_event_role_info);

            $historyData = array(
                'ID_EVENTS'         => $eventId,
                'ID_USERS'          => $resultProfile->ID_USERS,
                'DATE_HISTORY'      => date('Y-m-d H:i:s')
            );
            
            History::create($historyData); //Menambah data riwayat keikutsertaan pada event tertentu
            $newHistoryId = DB::getPdo()->lastInsertId();
        
            $total_point = 0;

            for ($i = 0; $i < count($eventRoleIdS); $i++) {
                $eventDetailId[$i] = $this->event->getEventDetailIdByParams($eventId, $eventRoleIdS[$i]);

                $detailHistoryData = array(
                    'ID_HISTORY'            => $newHistoryId,
                    'ID_EVENT_DETAIL'       => $eventDetailId[$i]->ID_EVENT_DETAIL,
                    'POINT_REACHED'         => $eventDetailId[$i]->POINT,
                    'HISTORY_DETAIL_INFO'   => $eventRoleInfoS[$i]
                );

                HistoryDetail::create($detailHistoryData); //Menambah data riwayat detail keikutsertaan pada event tertentu

                $total_point = $total_point + $eventDetailId[$i]->POINT;
            }

            $this->user->updatePointUser($total_point + $recentPoint, $userId); //Menambah poin pada user tertentu.

            $data['status'] = true;
            $data['message'] = 'Join Event Success!';
            $data['point_earned'] = $total_point;
            // }
        } else {
            $data['status'] = false;
            $data['message'] = 'Api Key is not valid!';
        }

        return $data;
    }
}
