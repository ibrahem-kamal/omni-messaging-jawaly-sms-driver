<?php

namespace Ibrahemkamal\OmniMessagingJawalySmsDriver;

use Ibrahemkamal\OmniMessaging\Facades\OmniMessaging;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OmniMessagingJawalySmsDriverServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('omni-messaging-jawaly-sms-driver');
    }

    public function registeringPackage()
    {

    }

    public function packageRegistered()
    {
        OmniMessaging::extend('jawaly', function ($app) {
            return new OmniMessagingJawalySmsDriver();
        });
    }
}
