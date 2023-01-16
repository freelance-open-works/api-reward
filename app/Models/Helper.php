<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;

class Helper extends Model
{
    /*  
        Model untuk tabel helper.  
        Isi tabel: Data-data pembantu, seperti data max redeem (jumlah maksimum pengguna dapat redeem barang dalam satu periode).
        Digunakan pada controller: User/CatalogueController, User/RedeemController
    */
    use HasFactory;
    public $timestamps = false;
    protected $table = 'helper';
    protected $fillable = [
        'NAME', 'VALUE'
    ];

    public function getHelperValue($name)
    {
        /* Mendapatkan data helper berdasarkan nama helper tertentu. Contohnya, MAX_REDEEM. */
        return DB::table('helper')
            ->select('VALUE')
            ->where('NAME', $name)
            ->first();
    }

    public function updateHelperValue($value, $name)
    {
         /* Memperbaharui data helper MAX_REDEEM berdasarkan nama helper tertentu. Contohnya, MAX_REDEEM. */
        return Helper::where('NAME', $name)->update(['MAX_REDEEM' => $value]);
    }
}
