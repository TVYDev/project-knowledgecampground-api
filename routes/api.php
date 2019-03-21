<?php

use Illuminate\Http\Request;
use App\Libs\MiddlewareConst;

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

Route::group([
    'prefix' => 'auth'
    ], function() {
        Route::post('login', 'UserController@login')->name('user.login');
        Route::post('register', 'UserController@register')->name('user.register');
        Route::middleware(MiddlewareConst::JWT_AUTH)->post('/logout', 'UserController@logout')->name('user.logout');
        Route::middleware(MiddlewareConst::JWT_AUTH)->get('/user', 'UserController@getUser')->name('user.getUser');
        Route::middleware(MiddlewareConst::JWT_AUTH)->post('/change-password', 'UserController@changePassword')->name('user.changePassword');
});

Route::group([
    'prefix' => 'user-avatar',
    'middleware' => MiddlewareConst::JWT_AUTH
    ], function() {
        Route::get('/user-avatar', 'UserAvatarController@getUserAvatar')->name('userAvatar.getUserAvatar');
});