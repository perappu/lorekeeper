<?php

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

Route::group(['namespace' => 'Api'], function () {

    Route::post('/token', 'AuthController@postGenerateToken');

    // TODO: Failing the power:api_access returns the HTML for the front page, would rather 401
    Route::group(['middleware' => ['auth:sanctum', 'power:api_access']], function() {

        Route::get('/user', 'InfoController@getUser');
        Route::get('/character', 'InfoController@getCharacter');

    });

});