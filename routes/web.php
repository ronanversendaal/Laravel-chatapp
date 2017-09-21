<?php

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

Auth::routes();

Route::get('/', 'ChatsController@index');
Route::get('messages', 'ChatsController@getMessages');
Route::post('messages', 'ChatsController@sendMessage');

Route::get('/threads', 'ThreadsController@index');
Route::get('/threads/messages', 'ThreadsController@getThreads');
Route::post('/threads/messages', 'ThreadsController@sendMessage');