<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Amtanesia;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\type;

class MessageController extends Controller
{
    /*
        Berisi fungsi untuk: mengirim chat/pesan di menu Customer Support.
        API dipanggil di proyek mobile: RestClient.cs
            View: MessageAct.xaml
            View Model: MessageViewModel.cs
    */
    public function __construct()
    {
        $this->message = new Message();
        $this->amtanesia = new Amtanesia();
    }


    public function getMessageByUserDesc(Request $request, $userId)
    {
        /*
		* Fungsi: Mendapatkan chat berdasarkan ID User. 
		 * Param:
		 *      - request:
		 *             - api_key    -> API key yang ada di parameter URL untuk autentikasi pengguna
		 * Return:
		 *      - data -> Array:
		 *             - status     -> Status API Key valid atau tidak
		 *             - message    -> Pesan pemanggilam API
         *             - chat       -> data chat antara pengguna tertentu dan admin
		 */

        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);

        if ($resultApiKey['status']) {
            $data['status'] = true;
            $data['message'] = 'Get message success!';
            $chat = $this->message->getMessageByUserDesc($userId);
            if (count($data) > 0) {
                $data['chat'] = $chat;
            } //return data semua history dalam bentuk json
            else {
                $data['chat'] = [];
            }
        } else {
            $data['status'] = false;
            $data['message'] = 'Api Key is not valid!';
        }

        return $data;
    }

    public function store(Request $request)
    {
        //Menyimpan data chat
        $apiKey = $request->input('api_key');
        $resultApiKey = $this->amtanesia->isApiKeyValid($apiKey);
        if ($resultApiKey['status']) {
            $data = $request->all();
            $validate = Validator::make($data, [
                'id_sender' => 'required',
                'message' => 'required',
            ]); //membuat rule validasi input

            if ($validate->fails()) {
                return response(['message' => $validate->errors()], 400); //return error invalid input
            }

            $data = Message::create($data); //menambah data pada product baru
            return response([
                'message' => 'Add Message Success',
                'data' => $data
            ], 201);
        } else {
            $data['status'] = false;
            $data['message'] = 'Api Key is not valid!';
        }

        return $data;
    }
}
