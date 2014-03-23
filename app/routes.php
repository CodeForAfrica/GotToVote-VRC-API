<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'SmsController@receiveSMS');
Route::get('/sms', 'SmsController@receiveSMS');
Route::get('/web', 'WebController@checkRegistered');

Route::get('/load/csv', 'LoadController@csvToCache');
Route::get('/load/csv/test', 'LoadController@testCsvToCache');
