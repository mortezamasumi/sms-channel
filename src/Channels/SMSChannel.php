<?php

namespace Mortezamasumi\SmsChannel\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Exception;

class SMSChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        try {
            $smsHandler = config('app.sms.operator', 'fake');

            throw_unless(method_exists($this, $smsHandler), new Exception('sms handler not found!', 500));
            throw_unless(method_exists($notification, 'smsText'), new Exception('no smsText method found!', 500));

            /** @disregard */
            $response = $this->$smsHandler($notification->smsText($notifiable), $notifiable->routes['sms']);

            if (method_exists($notification, 'succeeded')) {
                /** @disregard */
                $notification->succeeded($response);
            }
        } catch (Exception $exception) {
            if (method_exists($notification, 'failed')) {
                /** @disregard */
                $notification->failed($exception);
            }
        }
    }

    protected function fake(string $text, string $to): array
    {
        Http::fake(['*' => Http::response(
            [
                'status' => [
                    'message' => __('sms-channel::sms-channel.accepted'),
                    'code'    => 200,
                ],
            ],
            200,
            ['Headers']
        )]);

        $response = Http::post('*');

        return [
            'message' => data_get($response->json(), 'status.message'),
            'to'      => $to,
            'code'    => data_get($response->json(), 'status.code'),
            'from'    => 'fake',
            'text'    => $text,
        ];
    }

    protected function fakeFail(string $text, string $to): array
    {
        throw new Exception('error in operator');
    }

    protected function sabanovin(string $text, string $to): array
    {
        $gateway = config('app.sms.api_number', 'fake');

        $response = Http::get(
            'https://api.sabanovin.com/v1/'
            . config('app.sms.api_key', 'fake')
            . '/sms/send.json?'
            . 'gateway=' . $gateway
            . '&to=' . (app()->isProduction() ? $to : config('app.sms.dev_number', '0'))
            . '&text=' . $text . config('app.sms.append_text', __('sms-channel::sms-channel.sms_append_text'))
        );

        $code    = data_get($response->json(), 'status.code');
        $message = data_get($response->json(), 'status.message');

        throw_if($code !== 200, new Exception($message, $code));

        return [
            'message' => $message,
            'to'      => $to,
            'code'    => $code,
            'from'    => $gateway,
            'text'    => $text,
        ];
    }
}
