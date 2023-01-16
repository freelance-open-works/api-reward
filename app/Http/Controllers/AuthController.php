<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Hash;
use Laravel\Passport\Passport;
class AuthController extends Controller
{
    /*
        Berisi fungsi login admin.
        API dipanggil di views: Login.vue
    */

    public function login(Request $request)
    { 
        /*
            Fungsi: untuk login admin.
            Input:
                - username      -> String
                - password `    -> string
            Output: 
                - message       -> String -> Pesan pemanggilan API
                - user          -> Object -> Data admin yang login
                - token_type    -> String -> Jenis token untuk header pemanggilan API
                - access_token  -> String -> Token login untuk header pemanggilan API
        */
        
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'username' => 'required',
            'password' => 'required'
        ]); //membuat rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input    


        if (!Auth::attempt($loginData))
            return response(['message' => 'Invalid Credentials'], 401); //return error gagal login    

        // Get the currently authenticated user...
        $user = Auth::user();

        $token = $user->createToken('Authentication Token')->accessToken; //generate token
       
        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token,

        ]); //return data user dan token dalam bentuk json

    }
}
