<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Elearningmodel extends Model
{
    /*  
        Model untuk tabel e-learning history.  
        Isi tabel: Data e-learning history (data riwayat kegiatan pada situs kuliah yang dilakukan oleh pengguna).
    */
    use HasFactory;
    public $timestamps = false;
    protected  $primaryKey = 'ID_ELEARNING_HISTORY';
    protected $table = 'elearning_history';
    protected $fillable = ['ID_USERS', 'ID_ELEARNING_CHALLENGE', 'ID_LOG_KULIAH', 'IP_ADDRESS', 'DATE_HISTORY', 'TYPE'];
    
}
