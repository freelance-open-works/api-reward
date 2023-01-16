<?php

namespace App\Http\Controllers;

use App\Models\UserReview;
use Illuminate\Http\Request;

class UserReviewController extends Controller
{
    /*
        Berisi fungsi untuk CRUD user review.
        API dipanggil di views: UserReview.vue
    */

    public function index()
    {
        $review = new UserReview();
        $data = $review->getAllReview(); //dari Model UserReview. 

        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }
}
