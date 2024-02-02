<?php

namespace Ibrahemkamal\OmniMessagingJawalySmsDriver\Tests;

use Ibrahemkamal\OmniMessagingJawalySmsDriver\OmniMessagingJawalySmsDriverServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'Ibrahemkamal\\OmniMessagingJawalySmsDriver\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            OmniMessagingJawalySmsDriverServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        $migration = include __DIR__.'/../database/migrations/create_omni-messaging-jawaly-sms-driver_table.php.stub';
        $migration->up();
        */
    }
}
