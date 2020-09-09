<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () use ($router) {
    return $router->app->version();
});
Route::post('/login', 'AuthController@login');
Route::post('/logout', 'AuthController@logout');
Route::post('/hero', 'HeroController@store');
Route::group(['middleware' => 'auth'], function(){
    Route::post('/gang', 'GangController@store');
    Route::post('/gang/join', 'GangController@join');
    Route::get('/gang/{id}', 'GangController@show'); //??
    Route::get('/gang/{id}/invite', 'GangController@invite');
    Route::put('/gang/{id}', 'GangController@update');
    


    Route::put('/hero', 'HeroController@update');
    Route::get('/hero/{id}', 'HeroController@show');
    Route::get('/hero', 'HeroController@showSelf');

    Route::post('/quest', 'QuestController@store');
    Route::get('/quest/{id}', 'QuestController@show');
    Route::put('/quest/{id}', 'QuestController@update');
    Route::put('/quest/{id}/progress', 'QuestController@progress');
    Route::put('/quest/{id}/pending', 'QuestController@pending');
    Route::put('/quest/{id}/complete', 'QuestController@complete');
    Route::put('/quest/{id}/decline', 'QuestController@decline');
    Route::put('/quest/{id}/reopen', 'QuestController@reopen');
    Route::put('/quest/{id}/delete', 'QuestController@delete');
});



