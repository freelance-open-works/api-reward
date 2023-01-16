<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    /*
        Berisi fungsi untuk mengelola data chat/pesan yang dikirim oleh pengguna dari menu Customer Support di mobile.
        API dipanggil di views MessageManager.vue
    */

    public function __construct()
    {
        $this->message = new Message();
    }

    public function getMessageByUser($userId)
    {
        /*
            Fungsi: Mendapatkan data chat/pesan berdasarkan ID User.
            Param: 
                - userId         -> integer -> ID User yang akan diambil data chat/pesannya
            Return: 
                - message       -> String -> Pesan pemanggilan API
                - data          -> Object -> Data chat
        */
        $data = $this->message->getMessageByUser($userId); //dari Model Message. Untuk mendapat chat/pesan berdasarkan ID User.
        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
            ], 200);
        } //return data semua history dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404); //return message data history kosong
    }

    public function updateOpened($userId)
    {
        /*
            Fungsi: Memperbaharui status chat apakah sudah dibuka/read atau belum.
            Param: 
                - userId         -> integer -> ID User yang akan diperbaharui status read chat-nya.
            Return: 
                - message       -> String -> Pesan pemanggilan API
        */

        if ($this->message->updateOpened($userId)) {
            return response([
                'message' => 'Update Opened Message Success',

            ], 200);
        } //return data yang telah diedit dalam bentuk json


        return response([
            'message' => 'Update Opened Message Failed',
        ], 400);  //return message saat produk gagal diedit

    }

    public function index()
    {
        //get 
        $data = $this->message->getUsersUnique(); //dari Model Message. Untuk mendapat data ID User (NPM) yang unique (tidak ada  2 data NPM yang sama).

        $data->map(function ($item) {
            if (!is_null(Message::where('id_sender', $item->id_sender)->where('opened', 0)->first())) {
                $item->opened = 0;
            } else {
                $item->opened = 1;
            }
            return $item;
        });
        // return $data;

        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
            ], 200);
        } //return data semua history dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404); //return message data history kosong
    }


    public function store(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'id_sender' => 'required',
            'id_receiver' => 'required',
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
    }

    public function update(Request $request, $id)
    {
        $data = Message::find($id); //mencari data product berdasar id
        if (is_null($data)) {
            return response([
                'message' => 'Message Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $updateData = $request->all(); //abil semua input dari api client
        $validate = Validator::make($updateData, [
            'id_sender' => 'required',
            'id_receiver' => 'required',
            'message' => 'required',
            'opened' => 'required',
            'Message' => 'required',
            'version' => 'required',
        ]); //rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input


        $update = array(
            'id_sender' => $updateData['ID_USERS'],
            'id_receiver' => $updateData['ALAMAT'],
            'message' => $updateData['JAM'],
            'opened' => $updateData['TANGGAL'],
            'Message' => $updateData['PENERIMA'],
            'version' => $updateData['ISI_PAKET'],
        );

        if ($data->update($update)) {
            return response([
                'message' => 'Update Message Success',
                'data' => $data
            ], 200);
        } //return data yang telah diedit dalam bentuk json


        return response([
            'message' => 'Update Message Failed',
            'data' => $data
        ], 400);  //return message saat produk gagal diedit
    }


    public function destroy($id)
    {
        $data = Message::find($id); //mencari data berdsaar id

        if (is_null($data)) {
            return response([
                'message' => 'Message Not Found',
                'data' => null
            ], 404); //return message data product tidak ditemukan
        }

        if ($data->delete()) {
            return response([
                'message' => 'Delete Message Success',
                'data' => $data
            ], 200); //return message data product berhasil dihapus
        }

        return response([
            'message' => 'Delete Message Failed',
            'data' => null
        ], 400); //return message data product gagal dihapus
    }
}
