<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () use ($router) {
    return $router->app->version();
});
Route::post('/login', 'AuthController@login');
Route::post('/logout', 'AuthController@logout');
Route::post('/hero', 'HeroController@store');
Route::group(['middleware' => 'auth'],function(){
    Route::post('/gang', 'GangController@store');
    Route::get('/gang/{id}', 'GangController@show');
    Route::get('/gang/{id}/heroes', 'GangController@heroes');
    Route::put('/gang/{id}', 'GangController@update');


    Route::put('/hero/{id}', 'HeroController@update');
    Route::get('/hero/{id}', 'HeroController@show');

    Route::post('/quest', 'QuestController@store');
    Route::put('/quest/{id}', 'QuestController@update');
    Route::put('/quest/{id}/progress', 'QuestController@progress');
    Route::get('/quest/{id}', 'QuestController@show');
});



