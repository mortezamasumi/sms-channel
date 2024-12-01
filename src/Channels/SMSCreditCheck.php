<?php

namespace Mortezamasumi\SmsChannel\Channels;

use Illuminate\Support\Facades\Http;
use Exception;

class SMSCreditCheck
{
    public function credit(): string|int
    {
        if (method_exists($this, $smsHandler = config('app.sms.operator', 'fake'))) {
            return $this->$smsHandler();
        } else {
            return 'N/A';
        }
    }

    protected function fake(): string|int
    {
        return 'N/A';
    }

    protected function sabanovin(): string|int
    {
        try {
            $response = Http::get(
                'https://api.sabanovin.com/v1/'
                . config('app.sms.api_key', '')
                . '/account/balance.json'
            );

            return $response->json()['entry']['balance'] * 10;
        } catch (Exception $exception) {
            return 'N/A';
        }
    }
}
