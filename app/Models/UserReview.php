<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserReview extends Model
{
    /**
     * Model untuk tabel user_review.
     * Isi tabel: data review yang di-submit oleh user dari aplikasi amtarewards.
     * Digunakan pada controller: UserReviewController, User/ReviewController
     */
    use HasFactory;
    public $timestamps = false;
    protected  $primaryKey = 'ID';
    protected $table = 'user_review';
    protected $fillable = ['DEVICE', 'ID_USERS', 'REVIEW', 'VERSION', 'TIME_CREATED'];

    public function getAllReview()
    {
        /* Mendapatkan semua data review. */
        return UserReview::all();
    }

    public function insertNewReview($data)
    {
        /* Menambah data review baru. */
        return UserReview::create($data);
    }
}
