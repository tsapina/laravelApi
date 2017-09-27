<?php

use App\User;
use \App\Http\Middleware\testMiddleware;
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


Route::post('/myToken','UserController@authenticate');




Route::group(['middleware' => ['checkPermissions']], function () {  

    Route::get('/api/users', ['permissions' => ['Read', 'Create', ''],   ]);
    Route::get('/api/users/{id}');
    Route::delete('/api/users/{id}');


    Route::group(['middleware' => ['inputValidator']], function () {   
        Route::put('/api/users/{id}','UserController@updateUserById');
        Route::post('/api/users', 'UserController@addUser');
    });

});


