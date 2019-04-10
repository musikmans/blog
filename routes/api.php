<?php

use Illuminate\Http\Request;
use App\Post;

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

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('posts', 'PostController@store');
    Route::put('posts/{post}', 'PostController@update');
    Route::delete('posts/{post}', 'PostController@delete');
});

Route::get('posts', 'PostController@index');
Route::get('posts/{post}', 'PostController@show');
Route::get('hashtags', 'HashtagController@index');
Route::get('hashtags/{hashtag}', 'HashtagController@search');
Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');