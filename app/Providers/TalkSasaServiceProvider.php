<?php

namespace App\Providers;

use App\Channels\TalkSasaChannel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

class TalkSasaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Notification::extend('talksasa', function ($app) {
            return $app->make(TalkSasaChannel::class);
        });
    }
} 