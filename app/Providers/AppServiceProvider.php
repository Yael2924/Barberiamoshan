<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;


use Illuminate\Support\Facades\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

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
        Paginator::useBootstrap();

        // Registrar el canal 'firebase'
    Notification::extend('firebase', function ($app) {
        return new class {
            public function send($notifiable, $notification)
            {
                $messaging = app('firebase.messaging');
                $message = $notification->toFirebase($notifiable);

                $messaging->send($message);
            }
        };
    });
    }
}
