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
    Route::post('posts/{post}/likes', 'LikeController@create')->middleware('likes');
    Route::delete('posts/{post}/likes', 'LikeController@delete');
    Route::post('posts/{post}/comments', 'CommentController@create');
    Route::put('posts/{post}/comments/{comment_id}', 'CommentController@edit');
    Route::delete('posts/{post}/comments/{comment_id}', 'CommentController@delete');
    Route::get('users/current', 'UserController@current');
});

Route::get('posts', 'PostController@index');
Route::get('posts/{post}', 'PostController@show');
Route::get('hashtags', 'HashtagController@index');
Route::get('hashtags/{hashtag}', 'HashtagController@search');
Route::post('register', 'Auth\RegisterController@register');
Route::options('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');
Route::get('/validate-token', function () {
    return ['data' => 'Token is valid'];
})->middleware('auth:api');