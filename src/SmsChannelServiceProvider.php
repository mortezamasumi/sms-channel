<?php

namespace Mortezamasumi\SmsChannel;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Mortezamasumi\SmsChannel\Channels\SMSChannel;

class SmsChannelServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Notification::extend('sms', fn() => new SMSChannel());

        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'sms-channel');
    }
}
