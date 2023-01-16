<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Message extends Model
{
    /*  
        Model untuk tabel messages.
        Isi tabel: data chat/pesan yang dikirim dari menu Customer Support pada mobile dan menu Message Manager pada web admin.
        Digunakan pada controller: User/MessageController, MessageController
    */
    use HasFactory;
    protected $table = 'messages';
    protected $fillable = ['id_sender', 'id_receiver', 'message', 'opened', 'device', 'version'];

    public function getCreatedAtAttribute()
    {
        /* Convert atribut created_at ke format Y-m-d H:i:s */
        if (!is_null($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute()
    {
        /* Convert atribut updated_at ke format Y-m-d H:i:s */
        if (!is_null($this->attributes['updated_at'])) {
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUsersUnique()
    {
        /* 
            Mendapatkan data ID User (unique) yang pernah mengirim pesan dari menu Customer Support.
            Daftar ID User ini ditampilan di tabel menu Message Manager pada web admin.
        */
        $admin = new Admin();
        $id_admin = $admin->getAllAdmin(); // dari Model Admin. 
        return DB::table('messages as m')
            ->join('users as u', 'u.ID_USERS', 'm.id_sender')
            ->whereNotIn('id_sender', $id_admin)
            ->distinct() //sender aja karena pertama kali yang message pasti student/teacher.
            ->get(['m.id_sender', 'u.NAME']);
    }

    public function getMessageByUser($id_user)
    {
        /* Mendapatkan data chat/pesan berdasarkan ID User (sebagai pengirim chat atau penerima chat). */
        return DB::table('messages')
            ->where('id_sender', $id_user)
            ->orWhere('id_receiver', $id_user)
            ->get();
    }

    public function getMessageByUserDesc($id_user)
    {
        /* Mendapatkan data chat/pesan berdasarkan ID User (sebagai pengirim chat atau penerima chat), dengan urutan ID secara descending. */
        return DB::table('messages')
            ->where('id_sender', $id_user)
            ->orWhere('id_receiver', $id_user)
            ->orderByDesc('id')
            ->get();
    }

    public function updateOpened($userId)
    {
        /* Memperbaharui data status chat/pesan menjadi sudah dibuka/dibaca oleh admin (belum dibaca: 0; sudah dibaca: 1). */
        return DB::table('messages')
            ->where('id_sender', $userId)
            ->update(['opened' => 1]);
    }
}
