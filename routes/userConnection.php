<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::get('/suggestions', [App\Http\Controllers\HomeController::class, 'suggestion']);
Route::get('/suggestions_data', [App\Http\Controllers\HomeController::class, 'suggestions_data']);
Route::get('/get_more_users', [App\Http\Controllers\HomeController::class, 'getMoreUsers']);
Route::post('/connect/{userId}', [App\Http\Controllers\HomeController::class, 'connectUser'])->name('connect');
Route::any('/send_request', [App\Http\Controllers\HomeController::class, 'sendrequest']);
Route::post('/withdraw-request/{requestId}', [App\Http\Controllers\HomeController::class, 'withdrawRequest'])->name('withdraw.request');
Route::get('/recieved', [App\Http\Controllers\HomeController::class, 'recieved']);
Route::post('/accept-request/{requestId}', [App\Http\Controllers\HomeController::class, 'acceptRequest'])->name('accept.request');
Route::get('/connection', [App\Http\Controllers\HomeController::class, 'connection']);
Route::post('/remove/{requestId}', [App\Http\Controllers\HomeController::class, 'remove'])->name('remove.request');
