<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', 'UserController@authenticate');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::middleware('platform')->group( function () {
        Route::get('complaints', 'SFCController@getComplaints');
        Route::get('complaint/show/{complaintId}', 'SFCController@getComplaint');
        Route::post('ack', 'SFCController@ack');
        Route::get('files', 'SFCController@getFiles');
        Route::post('complaint/create', 'SFCController@createComplaint');
        Route::post('file/upload', 'SFCController@fileUpload');
        Route::post('complaint/update', 'SFCController@updateComplaint');
    });
});
