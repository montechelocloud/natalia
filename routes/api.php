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

//? Ruta de autenticacion para terceros
Route::post('login', 'UserController@authenticate');

//? Rutas para el consumo desde el CRM
Route::prefix('sfc')->group( function () {
    Route::get('complaints', 'SFCController@getComplaints');
    Route::get('complaints/{complaintId}', 'SFCController@getComplaint');
    Route::post('ack', 'SFCController@ack');
    Route::get('complaints/files/{complaintId}', 'SFCController@getComplaintFiles');
    Route::get('files/{fileId}', 'SFCController@getFile');
    Route::post('complaints', 'SFCController@createComplaint');
    Route::post('files', 'SFCController@fileUpload');
    Route::put('complaints', 'SFCController@updateComplaint');
});

//? Rutas para el consumo desde terceros
Route::middleware('jwt.verify')->prefix('ssv')->group( function () {
    Route::post('complaints', 'SSVController@createComplaint');
    Route::put('complaints', 'SSVController@updateComplaint');
});
