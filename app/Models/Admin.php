<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;

class Admin extends Authenticatable
{
    /*
        Model untuk tabel admins.
        Isi tabel: Akun admin.
    */

    use HasFactory, Notifiable, HasApiTokens;
    protected $table = 'admins';
    protected $fillable = [
        'username', 'password','role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getCreatedAtAttribute()
    {
        /*
            Mengubah format tanggal untuk kolom created_at menggunakan library Carbon
        */

        if (!is_null($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute()
    {
        /*
            Mengubah format tanggal untuk kolom updated_at menggunakan library Carbon
        */
        if (!is_null($this->attributes['updated_at'])) {
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getAllAdmin()
    {
        /*
            Mendapatkan data semua ID admin.
            Return: Array
        */
        return Admin::select('id')->get()->toArray();
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
