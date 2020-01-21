<?php

use Illuminate\Http\Request;

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

Route::post('/login', 'UserController@login');

Route::post('/register', 'UserController@create_worker');
Route::middleware('auth:api')->post('/user','UserController@create_admin');

Route::middleware('auth:api')->get('/user','UserController@get');
Route::middleware('auth:api')->get('/user/{id}', 'UserController@get');

Route::middleware('auth:api')->post('/job', 'JobController@create');

Route::middleware('auth:api')->post('/shift', 'ShiftController@create');
