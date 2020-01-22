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

Route::post('/login', 'UserController@get_token');

Route::post('/register', 'UserController@create_worker');

Route::middleware('auth:api')->get('/user', 'UserController@get_user');

Route::middleware('auth:api')->post('/admin','UserController@create_admin');
Route::middleware('auth:api')->get('/admin', 'UserController@get_admin');
Route::middleware('auth:api')->get('/admin/{id}', 'UserController@get_admin');

Route::middleware('auth:api')->get('/worker/', 'UserController@get_worker');
Route::middleware('auth:api')->get('/worker/{id}', 'UserController@get_worker');

Route::middleware('auth:api')->post('/job', 'JobController@create');
Route::middleware('auth:api')->get('/job', 'JobController@get');
Route::middleware('auth:api')->get('/job/{id}', 'JobController@get');
// UPDATE JOB

Route::middleware('auth:api')->post('/shift', 'ShiftController@create');
Route::middleware('auth:api')->get('/shift', 'ShiftController@get');
Route::middleware('auth:api')->get('/shift/{id}', 'ShiftController@get');
// UPDATE SHIFT

Route::middleware('auth:api')->post('/application', 'ApplicationController@create');
Route::middleware('auth:api')->get('/application', 'ApplicationController@get');
// DELETE APPLICATION
Route::middleware('auth:api')->post('/application/accept', 'ApplicationController@accept');
// DECLINE APPLICATION
