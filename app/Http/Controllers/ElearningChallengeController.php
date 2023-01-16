<?php

namespace App\Http\Controllers;

use App\Models\Amtanesia;
use App\Models\ElearningChallenge;
use Illuminate\Http\Request;

class ElearningChallengeController extends Controller
{
    /*
        Berisi fungsi untuk CRUD E-learning Challenge.
        API dipanggil di views ElearningChallengeManager.vue
    */

    public function __construct()
    {
        $this->chall = new ElearningChallenge();
    }

    public function getAllMoodleEventList()
    {
         /*
            Fungsi: mendapat data event moodle.
            Return: 
                - message       -> String -> Pesan pemanggilan API
                - data          -> Object -> Data event moodle
        */

        $data = $this->chall->getAllMoodleEventList(); //dari Model ElearningChallenge. Untuk  mendapat data event moodle.
        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }

    public function getAllKuliahLogDisplay()
    {
         /*
            Fungsi: mendapat data old event moodle. Data ini ditampilkan di formulir Elearning Manager.
            Return: 
                - message       -> String -> Pesan pemanggilan API
                - data          -> Object -> Data old event moodle
        */
        $data = $this->chall->getAllKuliahLogDisplay(); //dari Model ElearningChallenge.  Mendapatkan semua data old event moodle dari tabel dbstudents (dari database moodle)
        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }


    public function index($dest)
    {
        $data = $this->chall->getAllElearningChallenge(null, $dest); //dari Model/ElearningChallenge.php

        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }


    public function store(Request $request)
    {
        $storeData = $request->all(); //Ambil data input dari request

        //Mengatur data yang akan disimpan di tabel.
        if ($storeData['LIMIT_LOG'] == "false")
            $storeData['LIMIT_LOG_VAL'] = null;

        if ($storeData['OLD_VER'] == "true") {
            if ($storeData['OLD_EVENT_TYPE_AVAIL'] == "true") {
                $module = $storeData['OLD_MODULE'];
                $action = $storeData['OLD_ACTION'];
                $id_category = 0;
                $inputCategory =  $module . " " . $action;
            } else {
                $category = $storeData['OLD_EVENT'];
                $ex_inputCat = explode("-", $category);
                $id_category = $ex_inputCat[0];
                $module = $ex_inputCat[1];
                $action = $ex_inputCat[2];
                $inputCategory = $ex_inputCat[1] . " - " . $ex_inputCat[2];
            }
        } else {
            $inputCategory = NULL;
            $id_category = NULL;
            $module = NULL;
            $action = NULL;
            error_log("masuk old ver else");
        }

        if ($storeData['NEW_VER'] == "true") {
            if ($storeData['NEW_EVENT_TYPE_AVAIL'] == "true") {
                $ev_name_display = $storeData['NEW_DISPLAY_NAME'];
                $ev_name_code = $storeData['NEW_CODE_NAME'];
                $id_mdl_event = 0;
            } else {
                $event = $storeData['NEW_EVENT'];
                $ex_inputCatEventList = explode("-", $event);
                $id_mdl_event = $ex_inputCatEventList[0];
                $ev_name_display = $ex_inputCatEventList[1] . "-" . $ex_inputCatEventList[2];
                $ev_name_code = $ex_inputCatEventList[3];
            }
        } else {
            $ev_name_display = NULL;
            $ev_name_code = NULL;
            $id_mdl_event = NULL;
        }

        //Send notification
        if ($storeData['NOTIFICATION'] == "true") {
            $amtanesia = new Amtanesia();
            //TODO: ganti topics jadi NEWS lagi kalo sudah fix.
            $topic = "/topics/NEWS";
            $notif = $amtanesia->sendNotification($topic, "New E-learning Challenge!", $storeData['MESSAGE']);
        }

        $data = array(
            'NAME' => $storeData['NAME'],
            'DESC' => $storeData['DESC'],
            'ID_CATEGORY' => $id_category,
            'CATEGORY' => $inputCategory,
            'MODULE' => $module,
            'ACTION' => $action,
            'ID_EVENTNAME' => $id_mdl_event,
            'EVENTNAME_DISPLAY' => $ev_name_display,
            'EVENTNAME_CODE' => $ev_name_code,
            'POINT' => $storeData['POINT'],
            "DESTINATION" => $storeData['DESTINATION'],
            "MAX_COUNT" => $storeData['LIMIT_LOG_VAL'],
            'ID_PERIOD' => $storeData['ID_PERIOD'],
        );

        $create = ElearningChallenge::create($data); //menambah data pada product baru
        return response([
            'message' => 'Add E-Learning Challenge Success',
            'data' => $create
        ], 200); //return message data product tidak ditemukan

    }


    public function update(Request $request, $id)
    {
        $data = ElearningChallenge::find($id); //mencari data product berdasar id
        if (is_null($data)) {
            return response([
                'message' => 'E-Learning Challenge Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $storeData = $request->all(); //abil semua input dari api client

        if ($storeData['LIMIT_LOG'] == "false")
            $storeData['LIMIT_LOG_VAL'] = null;

        if ($storeData['OLD_VER'] == "true") {
            if ($storeData['OLD_EVENT_TYPE_AVAIL'] == "true") {
                $module = $storeData['OLD_MODULE'];
                $action = $storeData['OLD_ACTION'];
                $id_category = 0;
                $inputCategory =  $module . " " . $action;
                error_log("masuk old event avail true");
            } else {
                $category = $storeData['OLD_EVENT'];
                $ex_inputCat = explode("-", $category);
                $id_category = $ex_inputCat[0];
                $module = $ex_inputCat[1];
                $action = $ex_inputCat[2];
                $inputCategory = $ex_inputCat[1] . " - " . $ex_inputCat[2];
                error_log("masuk old event avail false");
            }
        } else {
            $inputCategory = NULL;
            $id_category = NULL;
            $module = NULL;
            $action = NULL;
            error_log("masuk old ver else");
        }

        if ($storeData['NEW_VER'] == "true") {
            if ($storeData['NEW_EVENT_TYPE_AVAIL'] == "true") {
                $ev_name_display = $storeData['NEW_DISPLAY_NAME'];
                $ev_name_code = $storeData['NEW_CODE_NAME'];
                $id_mdl_event = 0;
                error_log("masuk new ver avail true");
            } else {
                $event = $storeData['NEW_EVENT'];
                $ex_inputCatEventList = explode("-", $event);
                error_log(sizeof($ex_inputCatEventList));
                $id_mdl_event = $ex_inputCatEventList[0];
                $ev_name_display = $ex_inputCatEventList[1] . "-" . $ex_inputCatEventList[2];
                $ev_name_code = $ex_inputCatEventList[3];
            }
        } else {
            $ev_name_display = NULL;
            $ev_name_code = NULL;
            $id_mdl_event = NULL;
            error_log("masuk new ver false");
        }

        $update = array(
            'NAME' => $storeData['NAME'],
            'DESC' => $storeData['DESC'],
            'ID_CATEGORY' => $id_category,
            'CATEGORY' => $inputCategory,
            'MODULE' => $module,
            'ACTION' => $action,
            'ID_EVENTNAME' => $id_mdl_event,
            'EVENTNAME_DISPLAY' => $ev_name_display,
            'EVENTNAME_CODE' => $ev_name_code,
            'POINT' => $storeData['POINT'],
            "DESTINATION" => $storeData['DESTINATION'],
            "MAX_COUNT" => $storeData['LIMIT_LOG_VAL'],
            'ID_PERIOD' => $storeData['ID_PERIOD'],
        );

        if ($data->update($update)) {
            return response([
                'message' => 'Update E-Learning Challenge Success',
                'data' => $data
            ], 200);
        } //return data yang telah diedit dalam bentuk json


        return response([
            'message' => 'Update E-Learning Challenge Failed',
            'data' => $data
        ], 400);  //return message saat produk gagal diedit
    }

    
    public function destroy($id)
    {
        $data = ElearningChallenge::find($id); //mencari data berdsaar id

        if (is_null($data)) {
            return response([
                'message' => 'E-Learning Challenge Not Found',
                'data' => null
            ], 404); //return message data product tidak ditemukan
        }

        if ($data->delete()) {
            return response([
                'message' => 'Delete E-Learning Challenge Success',
                'data' => $data
            ], 200); //return message data product berhasil dihapus
        }

        return response([
            'message' => 'Delete E-Learning Challenge Failed',
            'data' => null
        ], 400); //return message data product gagal dihapus
    }
}
