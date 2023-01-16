<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Device extends Model
{
    /* 
        Model untuk tabel device_version.  
        Isi tabel: Data nama dan versi device, seperti Android dan iOS.
        Digunakan pada controller: DeviceController, User/LoginController
    */
    use HasFactory;

    public $timestamps = false;
    protected  $primaryKey = 'ID_DEVICE_VERSION';
    protected $table = 'device_version';
    protected $fillable = ['DEVICE', 'VERSION'];

    public function getAllDevice()
    {
        /* 
            Mendapatkan semua data device.
        */
        return Device::all();
    }

    //OTHERS 
    public function getDeviceVersion($device)
    {
        /* 
            Mendapatkan versi device berdasarkan nama device
        */
        return DB::table('device_version')->where('DEVICE', $device)->first();
    }
}
