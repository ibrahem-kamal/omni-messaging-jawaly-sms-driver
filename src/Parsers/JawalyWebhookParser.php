<?php

namespace Ibrahemkamal\OmniMessagingJawalySmsDriver\Parsers;

use Ibrahemkamal\OmniMessaging\Common\Parsers\AbstractWebhookParse;
use Ibrahemkamal\OmniMessaging\Common\Parsers\Resources\SmsNumber;

class JawalyWebhookParser extends AbstractWebhookParse
{
    public function parsePayload(array $payload): AbstractWebhookParse
    {
        $smsMessage = $payload['sms_message'];
        $numbers = $payload['numbers'];
        foreach ($numbers as $number) {
            $parsedNumber = new SmsNumber();
            $parsedNumber->setNumber($number['number'])
                ->setReference($number['msg_id'])
                ->setFrom($smsMessage['sender_name'])
                ->setIsSuccess($number['status'] == 3)
                ->setError($number['error_code_string'] ?? '');
            $this->addParsedNumber($parsedNumber);
        }

        return $this;
    }
}
