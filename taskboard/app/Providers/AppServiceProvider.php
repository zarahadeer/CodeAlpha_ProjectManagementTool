<?php

namespace App\Providers;

use App\Events\NotificationReceived;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Whenever a notification is saved, also push it to that user's browser.
        DatabaseNotification::created(function (DatabaseNotification $notification) {
            try {
                event(new NotificationReceived(
                    userId: (int) $notification->notifiable_id,
                    id: $notification->id,
                    message: $notification->data['message'] ?? '',
                    url: route('notifications.open', $notification->id, false),
                    unread: $notification->notifiable->unreadNotifications()->count(),
                ));
            } catch (\Throwable $e) {
                // Reverb not running? The notification is still saved; just not live.
                report($e);
            }
        });
    }
}