<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('auth/login', 'AuthController@login');
Route::post('auth/password-recover', 'AuthController@forgetPassword');
Route::post('auth/password-reset/{token}', 'AuthController@changePasswordByToken');
Route::get('health', function () { return [];});

Route::group([
    'middleware' => 'auth:api',
], function ($router) {
    Route::post('auth/logout', 'AuthController@logout');
    Route::post('auth/refresh', 'AuthController@refresh');
    Route::get('auth/me', 'AuthController@me');

    // Users
    Route::post('users/change-password', 'UsersController@changePassword');
});
