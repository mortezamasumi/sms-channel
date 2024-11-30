<?php

namespace Mortezamasumi\SmsChannel\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class SMSChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (method_exists($this, $smsHandler = config('app.sms.operator', 'fake'))) {
            $response = $this->$smsHandler(
                $notification->toSms($notifiable),
                $notifiable->routes['sms']
            );
        } else {
            throw (new \Exception('sms handler not found!', 500));
        }

        if (method_exists($notification, 'succeeded')) {
            $notification->succeeded($response);
        }
    }

    public function fake(string $text, string $to): array
    {
        Http::fake(['*' => Http::response(
            [
                'status' => [
                    'message' => 'تایید شد',
                    'code'    => 200,
                ],
            ],
            200,
            ['Headers']
        )]);

        $response = Http::post('*');

        return [
            'message' => $response->json()['status']['message'],
            'to'      => $to,
            'code'    => $response->json()['status']['code'],
            'from'    => 'fake',
            'text'    => $text,
        ];
    }

    public function sabanovin(string $text, string $to): array
    {
        $response = Http::get(
            'https://api.sabanovin.com/v1/'
            . config('app.sms.api_key', 'fake')
            . '/sms/send.json?'
            . 'gateway=' . config('app.sms.api_number', 'fake')
            . '&to=' . (app()->isProduction() ? $to : config('app.sms.dev_number', '0123456789'))
            . '&text=' . $text . config('app.sms.append_text', '\n\n\n لغو ۱۱')
        );

        throw_if(
            $response->json()['status']['code'] !== 200,
            new \Exception(
                $response->json()['status']['message'],
                $response->json()['status']['code']
            )
        );

        return [
            'message' => $response->json()['status']['message'],
            'to'      => $to,
            'code'    => $response->json()['status']['code'],
            'from'    => config('app.sms.api_number', 'fake'),
            'text'    => $text,
        ];
    }
}
