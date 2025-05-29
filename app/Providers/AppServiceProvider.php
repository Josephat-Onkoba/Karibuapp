<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use App\Services\TalkSasaSmsService;
use Illuminate\Notifications\ChannelManager;

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
    public function boot(): void
    {
        // Register the custom TalkSasa notification channel
        Notification::extend('talksasa', function ($app) {
            return new class($app->make(TalkSasaSmsService::class)) {
                protected $smsService;

                public function __construct(TalkSasaSmsService $smsService)
                {
                    $this->smsService = $smsService;
                }

                public function send($notifiable, $notification)
                {
                    if (!method_exists($notification, 'toTalkSasa')) {
                        throw new \RuntimeException('Notification is missing toTalkSasa method');
                    }

                    $message = $notification->toTalkSasa($notifiable);
                    
                    return $this->smsService->sendSms(
                        $message['phone'],
                        $message['message']
                    );
                }
            };
        });
    }
}
