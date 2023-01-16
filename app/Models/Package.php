<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Package extends Model
{
    /**
     * Model untuk tabel package.
     * Isi tabel: data formulir paket yang akan dikirimkan ke pengguna amtarewards.
     * Digunakan pada controller: PackageController.    
     */

    use HasFactory;
    protected  $primaryKey = 'ID_PACKAGE';
    protected $table = 'package';
    protected $fillable = ['NAME', 'ID_USERS', 'JAM', 'TANGGAL', 'ALAMAT', 'PENERIMA', 'KODE_KIRIM', 'ISI_PAKET', 'ID_PERIOD'];

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

    public function getPackageName($id)
    {
        /* Mendapatkan nama-nama dari produk yang ada di formulir paket tertentu. */

        $package = Package::find($id); //Mencari data paket berdasarkan ID
        $decode = json_decode($package->ISI_PAKET); //ISI_PAKET berisi string array ID produk pada tabel catalogue. String array tersebut perlu di-decode untuk menjadi array lagi.

        return Redeem::join('catalogue as c', 'c.ID_CATALOGUE', 'redeem_log.ID_CATALOGUE')
            ->whereIn('redeem_log.ID_REDEEM_LOG', $decode)
            ->select('c.NAME_CATALOGUE')->get();
    }

    public function getOtherPackage($id)
    {
        /**
         *  Mendapatkan data paket user tertentu yang statusnya sudah on process atau finished. 
         *   Untuk men-disable checkbox apabila barang sudah on process / finished di form lain. 
         */

        $package = Package::find($id); //Mencari data paket berdasarkan ID
        $decode = json_decode($package->ISI_PAKET); //ISI_PAKET berisi string array ID produk pada tabel catalogue. String array tersebut perlu di-decode untuk menjadi array lagi.

        return Redeem::join('catalogue as c', 'c.ID_CATALOGUE', 'redeem_log.ID_CATALOGUE')
            ->whereNotIn('redeem_log.ID_REDEEM_LOG', $decode) //Kondisi untuk exclude data redeem yang ada di ISI_PAKET.
            ->where('redeem_log.ID_USERS', $package->ID_USERS)
            ->where('c.ID_PERIOD', $package->ID_PERIOD)
            ->where(function ($query) {
                $query->where('redeem_log.ID_REDEEM_STATUS', 3)
                    ->orWhere('redeem_log.ID_REDEEM_STATUS', 2);
            })
            ->select('redeem_log.ID_REDEEM_LOG', 'c.NAME_CATALOGUE', 'redeem_log.ID_REDEEM_STATUS')
            ->get();
    }

    public function getPackageFormBasedOnPeriode($periode)
    {
        /**
         * Mendapatkan data formulir paket berdasarkan ID periode.
         */
        return Package::join('users AS u', 'package.ID_USERS', 'u.ID_USERS')
            ->select(
                'package.ID_PACKAGE',
                'u.ID_USERS',
                'u.NAME',
                'package.ALAMAT',
                'package.TANGGAL',
                'package.JAM',
                'package.PENERIMA',
                'package.KODE_KIRIM',
                'package.ISI_PAKET',
                'package.ISI_PAKET_NAME',
                'package.ID_PERIOD'
            )
            ->where('package.ID_PERIOD', $periode)
            ->get();
    }
}
