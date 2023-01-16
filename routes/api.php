<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('login', 'AuthController@login');
Route::get('laporanstok', 'CatalogueExportController@export')->name('drc.export');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('review', 'ReviewController@index');
    Route::post('review', 'ReviewController@store');

    //Users
    Route::get('users', 'UserController@index');
    Route::get('users/{id}', 'UserController@getDetailUser');
    Route::get('id-users', 'UserController@getAllIdUsers');
    Route::delete('users/{id}', 'UserController@destroy');
    Route::get('leaderboardPeriode/{id}', 'UserController@getLeaderboardByPeriode');
        

    //User Challenge History
    Route::get('challenge-history', 'UserChallengeHistoryController@getAllHistory');
    Route::get('challenge-history-detail/{id}', 'UserChallengeHistoryController@getDetailHistory');

    //E-learning Challenge History
    Route::get('elearn', 'ElearningHistoryController@index');
    Route::get('elearnSearch', 'ElearningHistoryController@search');
    Route::get('elearn/{id}', 'ElearningHistoryController@getDetailHistoryElearn');

    //Catalogue
    Route::get('catalogue', 'CatalogueController@index');
    Route::post('catalogue', 'CatalogueController@store');
    Route::get('catalogues/{id}', 'CatalogueController@getDetailCatalogue');
    Route::post('catalogue/{id}', 'CatalogueController@update');
    Route::delete('catalogue/{id}', 'CatalogueController@destroy');

    //Catalogue Type
    Route::get('catalogue_type', 'CatalogueTypeController@index');
    Route::post('catalogue_type', 'CatalogueTypeController@store');
    Route::post('catalogue_type/{id}', 'CatalogueTypeController@update');
    Route::delete('catalogue_type/{id}', 'CatalogueTypeController@destroy');

    //Catalogue Real Stock
    Route::get('catalogue_stock', 'CatalogueStockController@index');
    Route::post('catalogue_stock/{id}', 'CatalogueStockController@update');

    //Laporan Stok Catalogue
    //Route::get('laporanstok', 'CatalogueExportController@export');

    //Periode Manager
    Route::get('periode', 'PeriodeController@getAllPeriode');
    Route::delete('periode/{id}', 'PeriodeController@destroy');
    Route::post('periode', 'PeriodeController@store');
    Route::post('periode/{id}', 'PeriodeController@update');

    //News Manager
    Route::get('news-manager', 'NewsController@index');
    Route::delete('news-manager/{id}', 'NewsController@destroy');
    Route::post('news-manager', 'NewsController@store');
    Route::post('news-manager/{id}', 'NewsController@update');
    Route::post('send-notif-news', 'NewsController@sendNotification');

    //Redeem Manager
    Route::get('redeem-manager', 'RedeemController@getAllRedeem');
    Route::put('redeem-manager/{id}', 'RedeemController@update');
    Route::put('maxredeem', 'RedeemController@maxredeem');

    //User Review
    Route::get('review', 'UserReviewController@index');

    //Device Manager
    Route::get('device', 'DeviceController@index');
    Route::delete('device/{id}', 'DeviceController@destroy');
    Route::post('device', 'DeviceController@store');
    Route::post('device/{id}', 'DeviceController@update');

    //E-learning Challenge Manager
    Route::get('elearn-challenge/{dest}', 'ElearningChallengeController@index');
    Route::get('moodle-event-list', 'ElearningChallengeController@getAllMoodleEventList');
    Route::get('kuliah-log-display', 'ElearningChallengeController@getAllKuliahLogDisplay');
    Route::post('elearn-challenge', 'ElearningChallengeController@store');
    Route::post('elearn-challenge/{id}', 'ElearningChallengeController@update');
    Route::delete('elearn-challenge/{id}', 'ElearningChallengeController@destroy');


    //Maintenance Manager
    Route::get('maintenance', 'MaintenanceController@getStatus');
    Route::post('maintenance', 'MaintenanceController@update');

    //Challenge Manager
    Route::get('challenge', 'ChallengeController@getAllEvent');
    Route::post('challenge', 'ChallengeController@store');
    Route::post('challenge/{id}', 'ChallengeController@update');
    Route::delete('challenge/{id}', 'ChallengeController@destroy');
    Route::get('challenge/{id}', 'ChallengeController@getDetailChallenge');

    //Package Manager
    ///////Halaman Data Redeem
    Route::get('package/{periodeId}', 'PackageController@index');
    Route::get('products/{userId}/{periodeId}', 'PackageController@getProductListUser');
    Route::get('products/{packageId}', 'PackageController@getOtherPackage');
    ///////Halaman Data Formulir
    Route::get('package-form', 'PackageController@indexForm');
    Route::get('package-form/{id}', 'PackageController@getPackageFormBasedOnPeriode');
    Route::get('package-name/{id}', 'PackageController@getPackageName');
    Route::post('package-form', 'PackageController@store');
    Route::post('package-form/{id}', 'PackageController@update');
    Route::delete('package-form/{id}', 'PackageController@destroy');

    Route::get('export', 'PackageController@exportExcel');

    //Message
    Route::get('message/sender-unique', 'MessageController@index');
    Route::get('message/{userId}', 'MessageController@getMessageByUser');
    Route::post('message', 'MessageController@store');
    Route::post('message/opened/{userId}', 'MessageController@updateOpened');
});

///////User API

//Login
Route::get('v2/info', 'User\LoginController@getamtarewardStatus');
Route::post('student/login', 'User\LoginController@login');

//Main
Route::get('student/profile', 'User\MainContrkuloller@getProfile');
Route::get('elearning/sync', 'User\MainController@generateSaveLogKuliah');
Route::get('elearning/syncAll', 'User\MainController@generateSaveLogKuliahAll');
Route::get('student/refreshPoint', 'User\MainController@refreshPoint');
Route::get('student/refreshPointAll', 'User\MainController@refreshPointAll');
Route::get('test', 'User\MainController@saveLogKuliahAll');

//History
Route::get('history/histories', 'User\HistoryController@getHistoryUser');
Route::get('elearning/history/get', 'User\HistoryController@getElearningHistoryUser');
//Detail Elearning History
Route::post('elearning/history/get/item', 'User\HistoryController@getItemElearningHistoryByIdChall');

//Leaderboard
Route::get('leaderboard/student', 'User\LeaderboardController@getLeaderboardStudent');
Route::get('leaderboard/teacher', 'User\LeaderboardController@getLeaderboardTeacher');

//Missmatch E-learning Challenge History
Route::get('missmatch', 'MissmatchElearnHistoryController@index');

//Catalogue
Route::get('catalogue/catalogues', 'User\CatalogueController@getCatalogue');
//Detail Catalogue
Route::post('redeem', 'User\RedeemController@userRedeem');

//Event
Route::get('event/events', 'User\EventController@getAllEvents');
Route::get('event/otherevents', 'User\EventController@getAllOtherEvents');
//Join Event
Route::post('event/join', 'User\EventController@joinEvent');

//News
Route::get('news', 'User\NewsController@getAllNews');

//Redeem
Route::get('get_redeem/user', 'User\RedeemController@getRedeemUser');

//Review
Route::post('review/submit', 'User\ReviewController@submitReview');

//Social
Route::post('updateUserSocial', 'User\SocialController@updateUserSocial');

//message
Route::get('chat/{userId}', 'User\MessageController@getMessageByUserDesc');
Route::post('chat', 'User\MessageController@store');

Route::apiResource('roles', 'RoleController');
