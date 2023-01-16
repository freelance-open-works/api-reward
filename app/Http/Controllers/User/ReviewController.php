<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amtanesia;
use App\Models\User;
use App\Models\UserReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /*
        Berisi fungsi untuk: submit review.
        API dipanggil di proyek mobile: RestClient.cs
            View: SettingAct.xaml
            View Model: SettingViewModel.cs
    */
    public function __construct()
    {
        $this->amtanesia = new Amtanesia();
        $this->user = new User();
        $this->review = new UserReview();
    }

    public function submitReview(Request $request)
    {
          /*
		* Fungsi: Pengguna submit review ke database. 
		 * Param:
		 *      - request:
         *             - api_key   -> API Key
		 *             - review    -> isi review yang dikirim oleh pengguna
         *             - device    -> jenis device yang digunakan (android/ios)
         *             - version  -> versi device
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
         *             - DEVICE     -> jenis device yang digunakan
         *             - REVIEW     -> isi review yang dikirim oleh pengguna
         *             - VERSION    -> versi device
		 */
        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);

        if ($resultApiKey['status']) {
            $submitData = $request->all();
            $resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);
            $idUser = $resultProfile->ID_USERS;

            $validate = Validator::make($submitData, [
                'review'          => 'required',
                'device'          => 'required',
                'version'         => 'required'
            ]);

            if ($validate->fails()) {
                $data['status'] = false;
                $data['message'] = 'Data input problem!';
                return response(['message' => $validate->errors(), 'data' => $data], 400); //return error invalid input
            }
            $review = $submitData['review'];
            $device = $submitData['device'];
            $version = $submitData['version'];

            $data = array(
                'DEVICE'   => $device,
                'ID_USERS' => $idUser,
                'REVIEW'   => $review,
                'VERSION'  => $version
            );

            $this->review->insertNewReview($data);
            $data['status'] = true;
            $data['message'] = 'Success submit a review!';
        } else {
            $data['status'] = false;
            $data['message'] = 'Api Key is not valid!';
        }

        //echo json_encode($data)
        return $data;
    }
}
