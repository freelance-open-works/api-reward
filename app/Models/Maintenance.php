<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    /*  
        Model untuk tabel maintenance_status.
        Isi tabel: data status apakah aplikasi amtarewards sedang down atau tidak.
        Digunakan pada controller: User/LoginController, MaintenanceController
    */
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $table = 'maintenance_status';
    protected $fillable = ['STATUS', 'UPDATE_TIME'];

    public function getRecentStatusValue()
    {
        /* Mendapatkan data status maintenance saat ini. */
        return Maintenance::first();
    }
}
