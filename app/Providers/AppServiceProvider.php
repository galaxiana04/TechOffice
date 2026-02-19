<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Http\Events\RequestHandled;

class AppServiceProvider extends ServiceProvider
{


    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if (app()->environment('production')) {
            Event::listen(RequestHandled::class, function ($event) {
                $event->response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
            });
        }
    }
}
