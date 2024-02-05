<?php

namespace Ibrahemkamal\OmniMessagingJawalySmsDriver\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ibrahemkamal\OmniMessagingJawalySmsDriver\OmniMessagingJawalySmsDriver
 */
class OmniMessagingJawalySmsDriver extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Ibrahemkamal\OmniMessagingJawalySmsDriver\OmniMessagingJawalySmsDriver::class;
    }
}
