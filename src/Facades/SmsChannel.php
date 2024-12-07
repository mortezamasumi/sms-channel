<?php

namespace Mortezamasumi\SmsChannel\Facades;

use Illuminate\Support\Facades\Facade;
use Mortezamasumi\SmsChannel\SmsChannelService;

class SmsChannel extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SmsChannelService::class;
    }
}
