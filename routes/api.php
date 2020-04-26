<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('v1')->group(function() {
    Route::post('login', 'LoginController@login');

    Route::apiResource('topics', 'TopicController')->middleware('auth:api');
});
