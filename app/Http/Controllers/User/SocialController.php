<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amtanesia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class SocialController extends Controller
{
    /*
        Berisi fungsi untuk: memperbaharui data sosial media yang dikoneksikan ke aplikasi amtarewards.
        API dipanggil di proyek mobile: RestClient.cs
            View: SettingAct.xaml
            View Model: SettingViewModel.cs
    */

    public function __construct()
    {
        $this->amtanesia = new Amtanesia();
        $this->user = new User();
    }

    public function updateUserSocial(Request $request)
    {
        /*
		* Fungsi: Untuk memperbaharui data pengguna yang log in ke twitter / facebook. 
		 * Param:
		 *      - request:
		 *             - social_type    -> jenis sosial media (twitter / facebook)
         *             - social_token   
         *             - active_status  -> status akun (tidak digunakan)
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
		 *             - ID_USERS         => ID User (NPM)
        *             - SOCIAL_TYPE     => jenis sosial media
                        - SOCIAL_TOKEN      
                        - TOKEN_SECRET    
                        - CREATE_TIME    => waktu pembuatan data
                        - UPDATE_TIME    => waktu terakhir diperbaharui
                        - ACTIVE_STATUS => status akun (tidak digunakan)
		 */

        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);
        if ($resultApiKey['status']) {  
            $resultProfile = $this->user->getProfileStudent($resultApiKey['device_id']);
            $updateData = $request->all();

            $validate = Validator::make($updateData, [
                'social_type'    => 'required',
                'social_token'     => 'required',
                'active_status' => 'required'
            ]);

            if ($validate->fails()) {
                $data['status'] = false;
                $data['message'] = 'Data input problem!';
                return response(['message' => $validate->errors(), 'data' => $data], 400); //return error invalid input
            }

            if ($updateData['social_type'] == 'twitter') {
                $validateToken = Validator::make($updateData, ['token_secret' => 'required']);

                if ($validateToken->fails()) {
                    $tokenValid['status'] = false;
                    $tokenValid['message'] = 'Data input token problem!';
                    return response(['message' => $validateToken->errors(), 'data' => $tokenValid], 400); //return error invalid input
                }

                $resultSocial = $this->user->getUserSocialByIdUser($resultProfile->ID_USERS, $updateData['social_type']);

                if (count($resultSocial) == 0) {
                    $userSocialValue = array(
                        'ID_USERS'         => $resultProfile->ID_USERS,
                        'SOCIAL_TYPE'     => $updateData['social_type'],
                        'SOCIAL_TOKEN'     => $updateData['social_token'],
                        'TOKEN_SECRET'     => $updateData['token_secret'],
                        'CREATE_TIME'    => date('Y-m-d H:i:s'),
                        'UPDATE_TIME'    => date('Y-m-d H:i:s'),
                        'ACTIVE_STATUS' => $updateData['active_status']
                    );
                    $this->user->insertUserSocial($userSocialValue);
                } else {
                    $userSocialValue = array(
                        'ID_USERS'         => $resultProfile->ID_USERS,
                        'SOCIAL_TYPE'     => $updateData['social_type'],
                        'SOCIAL_TOKEN'     => $updateData['social_token'],
                        'TOKEN_SECRET'     => $updateData['token_secret'],
                        'UPDATE_TIME'    => date('Y-m-d H:i:s'),
                        'ACTIVE_STATUS' => $updateData['active_status']
                    );
                    $this->user->updateUserSocial($userSocialValue, $resultProfile->ID_USERS, $updateData['social_type']);
                }

                $data['status'] = true;
                $data['message'] = 'Update user social success!';
            } else {
                $resultSocial = $this->user->getUserSocialByIdUser($resultProfile->ID_USERS, $updateData['social_type']);

                if (count($resultSocial) == 0) {
                    $userSocialValue = array(
                        'ID_USERS'         => $resultProfile->ID_USERS,
                        'SOCIAL_TYPE'     => $updateData['social_type'],
                        'SOCIAL_TOKEN'     => $updateData['social_token'],
                        'UPDATE_TIME'    => date('Y-m-d H:i:s'),
                        'CREATE_TIME'    => date('Y-m-d H:i:s'),
                        'ACTIVE_STATUS' => $updateData['active_status']
                    );
                    $this->user->insertUserSocial($userSocialValue);
                } else {
                    $userSocialValue = array(
                        'ID_USERS'         => $resultProfile->ID_USERS,
                        'SOCIAL_TYPE'     => $updateData['social_type'],
                        'SOCIAL_TOKEN'     => $updateData['social_token'],
                        'UPDATE_TIME'    => date('Y-m-d H:i:s'),
                        'ACTIVE_STATUS' => $updateData['active_status']
                    );
                    $this->user->updateUserSocial($userSocialValue, $resultProfile->ID_USERS, $this->input->post('social_type'));
                }

                $data['status'] = true;
                $data['message'] = 'Update user social success!';
            }
        } else {
            $data['status'] = false;
            $data['message'] = 'Api Key is not valid!';
        }

        echo json_encode($data);
    }
}
