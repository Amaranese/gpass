<?php

use Illuminate\Http\Request;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('register', 'RegisterController@register');

Route::post('login', 'LoginController@login');

Route::apiResource('category','CategoryController');

Route::apiResource('password','PasswordController');
