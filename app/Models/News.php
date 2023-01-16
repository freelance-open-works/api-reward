<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class News extends Model
{
    /*  
        Model untuk tabel news.
        Isi tabel: data berita yang ditambahkan oleh admin.
        Digunakan pada controller: NewsController.
    */
    use HasFactory;
    public $timestamps = false;
    protected  $primaryKey = 'ID_NEWS';
    protected $table = 'news';
    protected $fillable = ['NEWS_TITLE', 'NEWS_DESCRIPTION', 'DATE', 'PHOTO_SMALL', 'PHOTO_MEDIUM', 'PHOTO_LARGE', 'ID_PERIOD', 'MESSAGE'];

    public function getAllNews()
    {
        /* Mendapatkan semua data berita. */
        return DB::table("news")->get();
    }

    public function getNewsByPeriode($start, $end)
    {
        /* Mendapatkan data berita berdasarkan tanggal dibuatnya berita tersebut (biasanya start date dan end date dari tabel periode). */
        return DB::table("news")
            ->where('DATE', '>=', $start)
            ->where('DATE', '<=', $end)
            ->get();
    }

    public function getAllNewsDesc()
    {
        /* Mendapatkan semua data berita yang diurutkan dari tanggal paling baru. */
        return DB::table("news")->orderByDesc('DATE')->get();
    }
}
