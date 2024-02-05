<?php

namespace Ibrahemkamal\OmniMessagingJawalySmsDriver;

use Ibrahemkamal\OmniMessaging\Common\AbstractMessagingDriver;
use Ibrahemkamal\OmniMessaging\Concerns\MessagingDriverResponse;
use Ibrahemkamal\OmniMessaging\Contracts\WebhookParserContract;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OmniMessagingJawalySmsDriver extends AbstractMessagingDriver
{
    private string $baseUrl = 'https://api-sms.4jawaly.com/api/v1/account/area';

    public function send(string $message, string $mobileNumber, string $sender, array $options = []): MessagingDriverResponse
    {
        return $this->sendMessage($message, $mobileNumber, $sender);
    }

    public function sendBulk(string $message, array $mobileNumbers, string $sender, array $options = []): MessagingDriverResponse
    {
        return $this->sendMessage($message, $mobileNumbers, $sender);
    }

    public function getBalance(string $sender = '', array $options = []): MessagingDriverResponse
    {
        //@TODO: remove the available options to documentations and remove the static values
        $response = $this->buildClient('get', $this->baseUrl.'/me/packages', [
            'is_active' => 'true',
            'order_by' => 'id',
            'order_by_type' => 'desc',
            'page' => 1,
            'page_size' => 10,
            'return_collections' => 1,
        ]);
        if ($response->successful() && $response->json('code') == 200) {
            return $this->messagingDriverResponse->setSuccess(true)->setData([
                'total_balance' => $response->json()['total_balance'],
                'items' => $response->json()['items'],
            ]);
        } else {
            return $this->messagingDriverResponse->setSuccess(false)->setErrors($response->json());
        }
    }

    public function getChannelName(): string
    {
        return 'jawaly';
    }

    private function sendMessage(string $message, string|array $mobileNumbers, string $sender)
    {
        $mobileNumbers = Arr::wrap(($mobileNumbers));
        $formattedNumbers = $this->formatPhoneNumbers($mobileNumbers);
        $response = $this->buildClient('post', $this->baseUrl.'/sms/send', [
            'messages' => [
                [
                    'text' => $message,
                    'numbers' => $formattedNumbers,
                    'sender' => $sender,
                ],
            ],
        ]);
        if ($response->successful()) {
            if (isset($response->json()['messages'][0]['err_text'])) {
                return $this->messagingDriverResponse->setSuccess(false)->setErrors([$response->json()['messages'][0]['err_text']]);
            } else {
                return $this->messagingDriverResponse->setSuccess(true)->setData(['job_id' => $response->json()['job_id']]);
            }
        } elseif ($response->status() == 400) {
            return $this->messagingDriverResponse->setSuccess(false)->setErrors($response->json('message'));
        } elseif ($response->status() == 422) {
            return $this->messagingDriverResponse->setSuccess(false)->setErrors([__('Empty message body')]);
        } else {
            return $this->messagingDriverResponse->setSuccess(false)->setErrors([__('Failed with status code :statusCode', ['statusCode' => $response->status()])]);
        }
    }

    private function buildClient(string $method, string $url, array $data = []): Response
    {
        $username = $this->getConfigOption('username');
        $password = $this->getConfigOption('password');

        return Http::withBasicAuth($username, $password)
            ->asJson()
            ->$method($url, $data);
    }

    public function getWebhookParser(): WebhookParserContract
    {
        return new Parsers\JawalyWebhookParser();
    }

    public function formatPhoneNumbers(array $mobileNumbers): array
    {
        $formattedNumbers = [];
        foreach ($mobileNumbers as $mobileNumber) {
            $formattedNumbers[] = Str::of($mobileNumber)->replaceFirst('966', '0')
                ->remove('+')->value();
        }

        return $formattedNumbers;
    }
}
