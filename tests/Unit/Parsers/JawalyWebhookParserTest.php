<?php

use Ibrahemkamal\OmniMessaging\Contracts\WebhookParserContract;
use Ibrahemkamal\OmniMessagingJawalySmsDriver\Parsers\JawalyWebhookParser;

test('it implements WebhookParserContract', function () {
    expect(app(JawalyWebhookParser::class))->toBeInstanceOf(WebhookParserContract::class);
});

test('it can parse the payload', function () {
    $parser = app(JawalyWebhookParser::class);
    $payload = json_decode(file_get_contents(__DIR__ . '/../Mock/Responses/WebhookPayload.json'), true);
    $parser->parsePayload($payload);
    expect($parser->getParsedNumbers())->toHaveCount(2);
    expect($parser->getParsedNumbers()[0]->getNumber())->toBe($payload['numbers'][0]['number']);
    expect($parser->getParsedNumbers()[0]->getReference())->toBe($payload['numbers'][0]['msg_id']);
    expect($parser->getParsedNumbers()[0]->getFrom())->toBe($payload['sms_message']['sender_name']);
    expect($parser->getParsedNumbers()[0]->isSuccess())->toBeFalse();
    expect($parser->getParsedNumbers()[0]->getError())->toBe('');
    expect($parser->getParsedNumbers()[1]->getNumber())->toBe($payload['numbers'][1]['number']);
    expect($parser->getParsedNumbers()[1]->getReference())->toBe($payload['numbers'][1]['msg_id']);
    expect($parser->getParsedNumbers()[1]->getFrom())->toBe($payload['sms_message']['sender_name']);
    expect($parser->getParsedNumbers()[1]->isSuccess())->toBeTrue();
    expect($parser->getParsedNumbers()[1]->getError())->toBe('');
});
