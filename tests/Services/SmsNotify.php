<?php

namespace Mortezamasumi\SmsChannel\Tests\Services;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use Exception;

class SmsNotify extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['sms'];
    }

    public function smsText(object $notifiable): string
    {
        return 'this is text!';
    }

    public function failed(Exception $exception): void
    {
        Cache::forever('notification-result', 'failed');
    }

    public function succeeded(array $response): void
    {
        Cache::forever('notification-result', 'succeeded');
    }
}
