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
// Route::post('complaints/create', function () {
//     dd($request);
// });
Route::prefix('sfc')->group( function () {
    Route::get('complaints', 'SFCController@getComplaints');
    Route::get('complaints/{complaintId}', 'SFCController@getComplaint');
    Route::get('complaints/files/{complaintId}', 'SFCController@getComplaintFiles');
    Route::post('complaints/create', 'SFCController@createComplaint');
    Route::post('ack', 'SFCController@ack');
    Route::get('files/{fileId}', 'SFCController@getFile');
    Route::post('files', 'SFCController@fileUpload');
    Route::put('complaints/update', 'SFCController@updateComplaint');
});

//? Rutas para el consumo desde terceros
Route::middleware('jwt.verify')->prefix('ssv')->group( function () {
    Route::post('complaints/create', 'SSVController@createComplaint');
    Route::put('complaints/update', 'SSVController@updateComplaint');
});
