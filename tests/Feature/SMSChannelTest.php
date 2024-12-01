<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Mortezamasumi\SmsChannel\Tests\Services\SmsNotify;

it('can send sms', function (): void {
    // Arrange
    Config::set('app.sms.operator', 'fake');
    Cache::forget('notification-result');

    // Act
    Notification::routes(['sms' => '1234567890'])->notify(new SmsNotify());

    // Assert
    expect(Cache::get('notification-result'))->toBe('succeeded');
});

it('fail on unknown operator', function (): void {
    // Arrange
    Config::set('app.sms.operator', 'tofail');
    Cache::forget('notification-result');

    // Act
    Notification::routes(['sms' => '1234567890'])->notify(new SmsNotify());

    // Assert
    expect(Cache::get('notification-result'))->toBe('failed');
});

it('fail on operator opration', function (): void {
    // Arrange
    Config::set('app.sms.operator', 'fakeFail');
    Cache::forget('notification-result');

    // Act
    Notification::routes(['sms' => '1234567890'])->notify(new SmsNotify());

    // Assert
    expect(Cache::get('notification-result'))->toBe('failed');
});
