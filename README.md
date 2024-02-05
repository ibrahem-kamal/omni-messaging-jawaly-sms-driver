# Jawaly Sms Driver for Omni Messaging

___
**this package is designed to work with [Omni Messaging](https://github.com/ibrahem-kamal/omni-messaging)**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ibrahem-kamal/omni-messaging-jawaly-sms-driver.svg?style=flat-square)](https://packagist.org/packages/ibrahem-kamal/omni-messaging-jawaly-sms-driver)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ibrahem-kamal/omni-messaging-jawaly-sms-driver/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ibrahem-kamal/omni-messaging-jawaly-sms-driver/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ibrahem-kamal/omni-messaging-jawaly-sms-driver/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ibrahem-kamal/omni-messaging-jawaly-sms-driver/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ibrahem-kamal/omni-messaging-jawaly-sms-driver.svg?style=flat-square)](https://packagist.org/packages/ibrahem-kamal/omni-messaging-jawaly-sms-driver)

[Jawaly Sms](https://www.4jawaly.com/) Driver for Omni Messaging.

## Installation

You can install the package via composer:

```bash
composer require ibrahem-kamal/omni-messaging-jawaly-sms-driver
```

Add this to your `omni-messaging.php` config file:

```php
   'channels' => [
        'jawaly'=> [
            'driver' => 'jawaly',
            'options' => [
                'username' => env('JAWALY_USERNAME'),
                'password' => env('JAWALY_PASSWORD'),
                'sender_name' => env('JAWALY_SENDER_NAME'),
            ]
        ]
    ]
```


## Usage

```php
$sms = OmniMessaging::driver('jawaly')->send($message,$mobileNumber,$sender,$options = []);
    $sms->isSuccess(); //bool
    $sms->getErrorsString(); // errors as string
    $sms->getErrors(); // errors as array
    $sms->getData(); // array of data returned from the gateway
    $sms->toArray(); // array of all the above
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [ibrahemkamal](https://github.com/ibrahem-kamal)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
