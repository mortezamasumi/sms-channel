<?php

namespace Mortezamasumi\SmsChannel\Tests;

use Mortezamasumi\SmsChannel\SmsChannelServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SmsChannelServiceProvider::class];
    }
}
