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
Route::post('dcf/login', 'DCFController@authenticate');

Route::prefix('sfc')->group( function () {
    Route::get('complaints', 'SFCController@getComplaints');
    Route::get('complaints/show/{complaintId}', 'SFCController@getComplaint');
    Route::post('ack', 'SFCController@ack');
    Route::get('files', 'SFCController@getFiles');
    Route::post('complaints/create', 'SFCController@createComplaint');
    Route::post('file/upload', 'SFCController@fileUpload');
    Route::post('complaints/update', 'SFCController@updateComplaint');
});

Route::middleware('jwt.verify')->prefix('ssv')->group( function () {
    Route::post('complaints/create', 'SSVController@createComplaint');
    Route::post('complaints/update', 'SSVdcfController@updateComplaint');
});
