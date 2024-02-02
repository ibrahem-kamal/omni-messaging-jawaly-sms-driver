<?php

use Ibrahemkamal\OmniMessaging\Common\AbstractMessagingDriver;
use Ibrahemkamal\OmniMessaging\Contracts\WebhookParserContract;
use Ibrahemkamal\OmniMessaging\Facades\OmniMessaging;
use Ibrahemkamal\OmniMessagingJawalySmsDriver\OmniMessagingJawalySmsDriver;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('omni-messaging.channels.jawaly', [
        'driver' => 'jawaly',
        'options' => [
            'username' => 'test-username',
            'password' => 'test-password',
            'sender' => 'test-sender',
        ],
    ]);
});

test('it implements AbstractMessagingDriver', function () {
    expect(new OmniMessagingJawalySmsDriver())->toBeInstanceOf(AbstractMessagingDriver::class);
});

test('it has the correct channel name', function () {
    expect((new OmniMessagingJawalySmsDriver())->getChannelName())->toBe('jawaly');
});

test('it can send a message', function () {
    $payload = json_decode(file_get_contents(__DIR__.'/Mock/Responses/SuccessResponse.json'), true);
    Http::fake([
        'api-sms.4jawaly.com/api/v1/account/area/sms/send' => Http::response($payload),
    ]);
    $response = sendSmsRequest();
    expect($response->isSuccess())->toBeTrue();
    expect($response->getData('job_id'))->toBe($payload['job_id']);
});

test('it can send a message to multiple numbers', function () {
    $payload = json_decode(file_get_contents(__DIR__.'/Mock/Responses/SuccessResponse.json'), true);
    Http::fake([
        'api-sms.4jawaly.com/api/v1/account/area/sms/send' => Http::response($payload),
    ]);
    $driver = new OmniMessagingJawalySmsDriver();
    $response = $driver->sendBulk('test message', ['96611111111111', '96611111111112'], 'test-sender');
    expect($response->isSuccess())->toBeTrue();
    expect($response->getData('job_id'))->toBe($payload['job_id']);
});

test('it can get the balance', function () {
    $payload = json_decode(file_get_contents(__DIR__.'/Mock/Responses/GetBalanceResponse.json'), true);
    Http::fake([
        'api-sms.4jawaly.com/api/v1/account/area/me/*' => Http::response($payload),
    ]);
    $driver = new OmniMessagingJawalySmsDriver();
    $response = $driver->getBalance();
    expect($response->isSuccess())->toBeTrue();
    expect($response->getData('total_balance'))->toBe($payload['total_balance']);
    expect($response->getData('items'))->toBe($payload['items']);
});

test('it can display getBalance errors', function () {
    Http::fake([
        'api-sms.4jawaly.com/api/v1/account/area/me/*' => Http::response([], 400),
    ]);
    $driver = new OmniMessagingJawalySmsDriver();
    $response = $driver->getBalance();
    expect($response->isSuccess())->toBeFalse();
    expect($response->getErrorsArray())->toBe([]);
});

test('it can display sending errors with success request code', function () {
    $payload = json_decode(file_get_contents(__DIR__.'/Mock/Responses/SuccessResponseWithSendingError.json'), true);
    Http::fake([
        'api-sms.4jawaly.com/api/v1/account/area/sms/send' => Http::response($payload, 200),
    ]);
    $response = sendSmsRequest();
    expect($response->isSuccess())->toBeFalse();
    expect($response->getErrorsString())->toBe($payload['messages'][0]['err_text']);
});

test('it can display sending errors', function () {
    $error400Response = json_decode(file_get_contents(__DIR__.'/Mock/Responses/InvalidSenderResponse.json'), true);
    $error422Response = json_decode(file_get_contents(__DIR__.'/Mock/Responses/EmptyMessageResponse.json'), true);
    Http::fake([
        'api-sms.4jawaly.com/api/v1/account/area/sms/send' => Http::sequence()
            ->push($error400Response, 400)
            ->push($error422Response, 422)
            ->push([], 500),
    ]);

    $response = sendSmsRequest();
    expect($response->isSuccess())->toBeFalse()->and($response->getErrorsString())->toBe($error400Response['message']);
    $response = sendSmsRequest();
    expect($response->isSuccess())->toBeFalse()->and($response->getErrorsString())->toBe('Empty message body');
    $response = sendSmsRequest();
    expect($response->isSuccess())->toBeFalse()->and($response->getErrorsArray())->toBe(['Failed with status code 500']);
});

function sendSmsRequest()
{
    $driver = new OmniMessagingJawalySmsDriver();

    return $driver->send('test message', '96611111111111', 'test-sender');
}

test('it returns webhook parser class', function () {
    $driver = new OmniMessagingJawalySmsDriver();
    expect($driver->getWebhookParser())->toBeInstanceOf(WebhookParserContract::class);
});

test('it returns the correct driver', function () {
    $driver = OmniMessaging::driver('jawaly');
    expect($driver)->toBeInstanceOf(OmniMessagingJawalySmsDriver::class);
});
test('it removes 966 and +966 from passed phone numbers', function () {
    $driver = new OmniMessagingJawalySmsDriver();
    expect($driver->formatPhoneNumbers(['96611111111111', '96611111111112']))->toBe(['011111111111', '011111111112']);
});

test('facade returns the correct class', function () {
    expect(\Ibrahemkamal\OmniMessagingJawalySmsDriver\Facades\OmniMessagingJawalySmsDriver::getFacadeRoot())->toBeInstanceOf(\Ibrahemkamal\OmniMessagingJawalySmsDriver\OmniMessagingJawalySmsDriver::class);
});
