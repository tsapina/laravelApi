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

Route::group(array('prefix' => 'api'), function()
{
    Route::group(array('prefix' => 'users', 'middleware' => 'checkPermissions'), function()
    {
        Route::get('/','UserController@getAllUsers');
        Route::get('{id}','UserController@getUserById');
        Route::delete('{id}','UserController@deleteUserById');
        
        Route::group(['middleware' => ['inputValidator']], function () {   
            Route::put('/{id}','UserController@updateUserById');
            Route::post('/', 'UserController@addUser');
        }); 

    });

});

 




