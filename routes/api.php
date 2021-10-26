<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/auth/register', 'Api\Auth\AuthController@register');
Route::post('/auth/token', 'Api\Auth\AuthController@auth');

Route::group([
    'namespace' => 'Api',
    'middleware' => ['auth:sanctum']
], function () {
    Route::get('/auth/me', 'Auth\AuthController@getme');
    Route::post('/auth/logout', 'Auth\AuthController@logout');
});
