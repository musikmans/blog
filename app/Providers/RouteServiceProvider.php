<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));

             Route::group([
                'middleware' => ['api', 'cors'],
                'namespace' => $this->namespace,
                'prefix' => 'api',
            ], function ($router) {
                Route::group(['middleware' => 'auth:api'], function() {
                    Route::post('posts', 'PostController@store');
                    Route::put('posts/{post}', 'PostController@update');
                    Route::delete('posts/{post}', 'PostController@delete');
                    Route::post('posts/{post}/likes', 'LikeController@create')->middleware('likes');
                    Route::delete('posts/{post}/likes', 'LikeController@delete');
                    Route::post('posts/{post}/comments', 'CommentController@create');
                    Route::put('posts/{post}/comments/{comment_id}', 'CommentController@edit');
                    Route::delete('posts/{post}/comments/{comment_id}', 'CommentController@delete');
                });
                
                Route::get('posts', 'PostController@index');
                Route::get('posts/{post}', 'PostController@show');
                Route::get('hashtags', 'HashtagController@index');
                Route::get('hashtags/{hashtag}', 'HashtagController@search');
                Route::post('register', 'Auth\RegisterController@register');
                Route::post('login', 'Auth\LoginController@login');
                Route::post('logout', 'Auth\LoginController@logout');
                Route::get('/validate-token', function () {
                    return ['data' => 'Token is valid'];
                })->middleware('auth:api');
            });
    }
}
