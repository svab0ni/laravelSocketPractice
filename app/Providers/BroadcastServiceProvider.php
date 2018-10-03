<?php

namespace App\Providers;

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();
//        $this->app['router']->group(['middleware' => 'custom'], function ($router) {
//            $router->post('/broadcasting/auth', function(Request $request) {
//                return auth($request);
//            });
//        });

        require base_path('routes/channels.php');
    }
}
