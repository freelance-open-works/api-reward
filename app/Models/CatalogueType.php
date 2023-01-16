<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogueType extends Model
{

    /*
        Model untuk tabel catalogue_type. 
        Isi tabel: Data tipe katalog (contoh: Grand Prize, normal).
    */

    use HasFactory;

    public $timestamps = false;
    protected  $primaryKey = 'ID_CTG_TYPE';
    protected $table = 'catalogue_type';
    protected $fillable = ['CTG_TYPE', 'CTG_MAX_REDEEM'];


    public function getAllCatalogueType()
    {
        /*
            Medapatkan semua data tipe katalog.
        */
        return CatalogueType::all();
    }

    public function insertNewCatalogueType($data)
    {
        /*
            Memasukkan data tipe katalog baru.
        */
        $data = CatalogueType::create($data);
    }

    public function updateCatalogType($data, $id)
    {
        /*
            MEmperbaharui data tipe katalog yang sudah ada.
        */
        $data = CatalogueType::find($id);
        $data->update($data); // FIXME: Kurang yakin bisa langsung semua atribut di-update atau tidak
    }
}
