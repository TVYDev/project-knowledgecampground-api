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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
//    'middleware' => 'kc.auth',
    'prefix' => 'auth'
    ], function() {
        Route::post('/register', 'UserController@register')->name('user.register');
        Route::post('/login', 'UserController@login')->name('user.login');
        Route::post('/logout', 'UserController@logout')->name('user.logout');
//        Route::get('/user', 'UserController@user')->name('user.user');
});