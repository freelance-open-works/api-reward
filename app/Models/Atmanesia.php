<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amtanesia extends Model
{
    /*
        Model untuk fungsi-fungsi general yang dapat dipakai di berbagai controller.
        Digunakan pada controller: ElearningChallengeController, NewsController, User/CatalogueController, User/EventController,
                                    User/LeaderboardController, User/MainController, User/MessageController, User/NewsController,
                                    User/RedeemController, User/ReviewController, User/SocialController
    */
    use HasFactory;

    public function isApiKeyValid($apiKey)
    {
        /*
            Memeriksa apakah API Key (yang didapat dari parameter pada URL) valid.
            Untuk proses autentikasi pemanggilan API di mobile.
            Return: Array
        */

        $user = new User();
        $result['device'] = $user->isApiKeyExist($apiKey); //dari Model/User.php. Untuk memeriksa apakah API key ada pada tabel user_device.
        if (!is_null($result['device'])) {
            $data['status'] = true;
            $data['device_id'] = $result['device']->ID_USER_DEVICE;
        } else {
            $data['status'] = false;
            $data['device_id'] = null;
            $data['message'] = 'Invalid api_key!';
        }
        return $data;
    }

    public function array_column(array $array, $columnKey, $indexKey = null)
    {
        /*  TODO_DOC
            _______
            Return: 
        */
        $result = array();
        foreach ($array as $subArray) {
            if (!is_array($subArray)) {
                continue;
            } elseif (is_null($indexKey) && array_key_exists($columnKey, $subArray)) {
                $result[] = $subArray[$columnKey];
            } elseif (array_key_exists($indexKey, $subArray)) {
                if (is_null($columnKey)) {
                    $result[$subArray[$indexKey]] = $subArray;
                } elseif (array_key_exists($columnKey, $subArray)) {
                    $result[$subArray[$indexKey]] = $subArray[$columnKey];
                }
            }
        }
        return $result;
    }

    public function sendNotification($topic, $title, $body)
    {
        /*   
            Mengirim notifikasi menggunakan Firebase.
            Return: Array
        */

        $ch = curl_init("https://fcm.googleapis.com/fcm/send");

        //Creating the notification array.
        $notification = array('title' => $title, 'body' => $body);

        //This array contains, the token and the notification. The 'to' attribute stores the token.
        $arrayToSend = array('to' => $topic, 'notification' => $notification);

        //Generating JSON encoded string form the above array.
        $json = json_encode($arrayToSend);

        //Setup headers:
        $headers = array();
        $headers[] = 'Content-Type:application/json';
        $headers[] = 'Authorization:key=AAAAULOjJEw:APA91bEHPtio5xNj0lEjP98HBDw-bs9BJTrI1J-LPN8ELijaBGYV2t3-GLUpE4cBcbqOMJrCuO6Cq3icb45EM_-R4OrwZqtMesseyOIBt_dHzcAcsh5hEtCu5xYcTrEfWM5pQ65hQYs_';

        //Setup curl, add headers and post parameters.
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        //Send the request
        curl_exec($ch);

        //Close request
        curl_close($ch);

        return $arrayToSend;
    }
}
