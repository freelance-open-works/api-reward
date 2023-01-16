<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Periode extends Model
{
    /**
     * Model untuk tabel year_period.
     * Isi tabel: data periode (sesuai dengan tahun ajaran).
     * Digunakan pada controller: PackageController, User/CatalogueController, User/EventController, User/HistoryController, User/MainController, User/NewsController, User/RedeemController
     */

    use HasFactory;

    public $timestamps = false;
    protected  $primaryKey = 'ID_PERIOD';
    protected $table = 'year_period';
    protected $fillable = ['YEAR_START', 'YEAR_FINISH', 'STATUS', 'YEAR_PERIODE'];

    public function getAllPeriode()
    {
        /* Mendapatkan semua data periode. */
        return DB::table('year_period')
            ->get();
    }

    public function getActiveYearPeriode(){
         /* Mendapatkan satu data periode yang aktif. */
        return Periode::where('STATUS','active')->first();
    }

    public function getActivePeriode(){
        /* Mengembalikan ID periode yang aktif. */
        $result = $this->getActiveYearPeriode(); 
        if(!is_null($result)){
            return $result->ID_PERIOD;
        }else{
            return $this->getFirstPeriode(); //Mengemmbalikan ID period pertama apabila tidak ada periode yang aktif.
        }
    }

    public function getFirstPeriode(){
        /* Mendapatkan data pertama tabel year_period. */
        return Periode::first()->ID_PERIOD;
    }

    public function getPeriodeById($id){
        /* Mendapatkan data periode berdasarkan ID. */
        return Periode::where('ID_PERIOD', $id)->first();
    }

}
