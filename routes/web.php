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
Route::get('/threads/{thread}', [
    'as' => 'thread.show',
    'uses' => 'ThreadsController@show'
]);
Route::post('/threads/actions', [
    'as' => 'thread.actions',
    'uses' => 'ThreadsController@setAction'
]);

Route::post('/threads/create', [
    'as' => 'thread.create',
    'uses' => 'ThreadsController@store'
]);
Route::get('/threads/{thread}/messages/', 'ChatsController@getMessagesForThread');
Route::post('/threads/messages', 'ThreadsController@sendMessage');
