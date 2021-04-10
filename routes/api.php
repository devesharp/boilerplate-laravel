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
Route::get('health', function () {
    return [];
});

Route::post('upload-image-s3', function () {
    $file = request()->file('file');
    $path = \Illuminate\Support\Facades\Storage::disk('s3')->put('images', $file);
    $modelS3 = \App\Models\S3Files::query()->create([
        'path' => $path,
        'size' => $file->getSize(),
    ]);
    return $modelS3->toArray();
});

Route::group([
    'middleware' => 'auth:api',
], function ($router) {
    Route::post('auth/logout', 'AuthController@logout');
    Route::post('auth/refresh', 'AuthController@refresh');
    Route::get('auth/me', 'AuthController@me');

    // Users
    Route::post('users/change-password', 'UsersController@changePassword');
});

//Route::post('events/search', 'EventsController@search');
//Route::get('events/{id}', 'EventsController@get');
//Route::post('events/{id}', 'EventsController@update');
//Route::post('events', 'EventsController@create');
//Route::delete('events/{id}', 'EventsController@delete');
