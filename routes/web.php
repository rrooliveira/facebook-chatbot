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

Route::get('/bot', function () {
    $text = (new \ChatBot\Domain\Message\Entities\Text(1))->message('Hello World!');
    dd($text);
});

Route::prefix('chatbot')->group(function () {
    Route::get('/webhook', 'ChatBotController@subscribe');
    Route::post('/webhook', 'ChatBotController@receiveMessage');
});
