# Query Auth

master: [![Build Status](https://travis-ci.org/jeremykendall/query-auth.png?branch=master)](https://travis-ci.org/jeremykendall/query-auth) develop: [![Build Status](https://travis-ci.org/jeremykendall/query-auth.png?branch=develop)](https://travis-ci.org/jeremykendall/query-auth)

Signature generation and validation for REST API query authentication

## API Query Authentication

Most APIs require some sort of query authentication: a method of signing API
requests with an API key and signature.  The signature is usually generated
using a shared secret.  When you're consuming an API, there are (hopefully) easy
to follow steps to create signatures. When you're writing your own API, you
have to whip up both server-side signature validation and a client-side
signature creation strategy. This library endeavors to handle both of those
tasks; signature creation and signature validation.

## Philosophy

Query Auth is intended to be -- and is written as -- a bare bones library.  Many of
niceties and abstractions you'd find in a fully featured API library or SDK are
absent here.  The point of the library is to provide you with the ability to
focus on writing your API in any way you see fit, without adding any additional
dependencies to the mix, while allowing you to hand off the query authentication
to this library.


## Sample Implementation

I've provided a [sample implementation of the Query Auth library](https://github.com/jeremykendall/query-auth-impl) 
in order to better demonstrate how one might employ the library, from both the
API consumer and API creator perspectives.

## Usage

There are three components to this library: Request signing for API consumers
and creators, request signature validation for API creators, and API key and
API secret generation.

### Request Signing

``` php
$factory = new QueryAuth\Factory();
$client = $factory->newClient();

$key = 'API_KEY';
$secret = 'API_SECRET';
$method = 'GET';
$host = 'api.example.com';
$path = '/resources';
$params = array('type' => 'vehicles');

$signedParameters = $client->getSignedRequestParams($key, $secret, $method, $host, $path, $params);
```

`Client::getSignedRequestParams()` returns an array of parameters to send via
the querystring (for `GET` requests) or the request body. The parameters are
those provided to the method (if any), plus `timestamp`, `key`, and `signature`.

### Signature Validation

``` php
$factory = new QueryAuth\Factory();
$server = $factory->newServer();

$secret = 'API_SECRET_FROM_PERSISTENCE_LAYER';
$method = 'GET';
$host = 'api.example.com';
$path = '/resources';
// querystring params or request body as an array,
// which includes timestamp, key, and signature params from the client's
// getSignedRequestParams method
$params = 'PARAMS_FROM_REQUEST'; 

$isValid = $server->validateSignature($secret, $method, $host, $path, $params);
```

`Server::validateSignature()` will return either true or false.  It might also
throw one of three exceptions:
* `MaximumDriftExceededException`: If timestamp is too far in the future
* `MinimumDriftExceededException`: It timestamp is too far in the past
* `SignatureMissingException`: If signature is missing from request params

Drift defaults to 15 seconds, meaning there is a 30 second window during which the
request is valid. The default value can be modified using `Server::setDrift()`.

### Key Generation

You can generate API keys and secrets in the following manner.

``` php
$factory = new QueryAuth\Factory();
$keyGenerator = $factory->newKeyGenerator();

// 40 character random alphanumeric string
$key = $keyGenerator->generateKey();

// 60 character random string containing the characters
// 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ./
$secret = $keyGenerator->generateSecret();
```

Both key and secret are generated using Anthony Ferrara's [RandomLib](https://github.com/ircmaxell/RandomLib)
random string generator.

## Installation

Package installation is handled by Composer.

* If you haven't already, please [install Composer](http://getcomposer.org/doc/00-intro.md#installation-nix)
* Create `composer.json` in the root of your project:

``` json
{
    "require": {
        "jeremykendall/query-auth": "dev-develop"
    }
}
```

* Run `composer install`
* Require Composer's `vendor/autoload` script in your bootstrap/init script

## Feedback and Contributions

* Feedback is welcome in the form of pull requests and/or issues.
* Contributions should generally follow the strategy outlined in ["Contributing
  to a project"](https://help.github.com/articles/fork-a-repo#contributing-to-a-project)
* Please submit pull requests against the `develop` branch

## Credits

* The Client, Signer, and ParameterCollection code are my own implementation of
the [Signature Version 2
implementation](https://github.com/aws/aws-sdk-php/blob/master/src/Aws/Common/Signature/SignatureV2.php)
from the [AWS SDK for PHP
2](https://github.com/aws/aws-sdk-php/blob/master/src/Aws/Common/Signature/SignatureV2.php).
As such, a version of the Apache License Version 2.0 is included with this
distribution, and the applicable portion of the AWS SDK for PHP 2 NOTICE file
is included.

* API key and API secret generation is handled by Anthony Ferrara's
[RandomLib](https://github.com/ircmaxell/RandomLib) random string generator.
