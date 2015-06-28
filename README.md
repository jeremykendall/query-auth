# Query Auth

master: [![Build Status](https://travis-ci.org/jeremykendall/query-auth.png?branch=master)](https://travis-ci.org/jeremykendall/query-auth) develop: [![Build Status](https://travis-ci.org/jeremykendall/query-auth.png?branch=develop)](https://travis-ci.org/jeremykendall/query-auth)

Signature generation and validation for REST API query authentication

## API Query Authentication

Most APIs require some sort of query authentication, frequently a method of signing API
requests with an API key and signature. The signature is usually generated
using a shared secret.  When you're consuming an API, there are (hopefully) easy
to follow steps to create signatures. When you're writing your own API, you
have to whip up both a server-side signature validation strategy and a client-side
signature creation strategy. This library endeavors to handle both of those
tasks for you.

## Sample Implementation

A [sample implementation of the Query Auth library](https://github.com/jeremykendall/query-auth-impl)
is available in order to better demonstrate how one might employ the library.

## Usage

There are three components to this library:

* Request signing
* Request validation
* API key and secret generation

Request signing and validation are made possible by the use of request adapters.

### Request Adapters

Query Auth request adapters wrap outgoing and incoming requests and adapt them to the
request interface that Query Auth expects.

#### Outgoing

Outgoing request adapters are used to facilitate request signing. There are
currently two available in the `QueryAuth\Request\Adapter\Outgoing` namespace:

* `GuzzleRequestAdapter` for use with Guzzle v3
* `GuzzleHttpRequestAdapter` for use with Guzzle v4

#### Incoming

Incoming request adapters are used to facilitate request validation. There is
currently one available in the `QueryAuth\Request\Adapter\Incoming` namespace:

* `SlimRequestAdapter` for use with Slim PHP v2

#### Custom

If you would prefer to use an HTTP library other than Guzzle, or if you prefer
to use an application framework other than Slim, you will need to write your own
request adapter(s). Please refer to the existing request adapters for examples.

### Request Signing

``` php
use GuzzleHttp\Client as GuzzleHttpClient;
use QueryAuth\Credentials\Credentials;
use QueryAuth\Factory;
use QueryAuth\Request\Adapter\Outgoing\GuzzleHttpRequestAdapter;

$factory = new Factory();
$requestSigner = $factory->newRequestSigner();
$credentials = new Credentials('key', 'secret');

// Create a GET request and set an endpoint
$guzzle = new GuzzleHttpClient(['base_url' => 'http://api.example.com']);
$request = $guzzle->createRequest('GET', '/endpoint');

// Sign the request
$requestSigner->signRequest(new GuzzleHttpRequestAdapter($request), $credentials);

// Send signed request
$response = $guzzle->send($request);
```

### Request Validation

``` php
use QueryAuth\Credentials\Credentials;
use QueryAuth\Factory;
use QueryAuth\Request\Adapter\Incoming\SlimRequestAdapter;

$factory = new Factory();
$requestValidator = $factory->newRequestValidator();
$credentials = new Credentials('key', 'secret');

// Get the Slim request (in the context of a Slim route, hook, or middleware)
$request = $app->request;

// $isValid is a boolean
$isValid = $requestValidator->isValid(new SlimRequestAdapter($request), $credentials);
```

`RequestValidator::isValid()` will return either true or false.  It might also
throw one of three exceptions:
* `DriftExceededException`: It timestamp is beyond +- `RequestValidator::$drift`
* `SignatureMissingException`: If signature is missing from request params
* `TimestampMissingException`: If timestamp is missing from request params

Drift defaults to 15 seconds, meaning there is a 30 second window during which the
request is valid. The default value can be modified using `RequestValidator::setDrift()`.

### Replay Attack Prevention

There are a number of strategies available to prevent [replay attacks](http://en.wikipedia.org/wiki/Replay_attack).
The strategy in place here follows this general outline:
* Validate incoming signature
* If the signature is valid, check the storage layer to see if that combination of
API key and signature have been used before
* If they have, the request is likely a replay attack and should be denied
* If they have not, persist the API key, signature, and an expiration timestamp
* Routinely purge records with a timestamp beyond the expiration time

**IMPORTANT**: The signature expiration timestamp should be greater than
maximum allowable drift.  Deleting a signature too soon can leave you vulnerable
to a replay attack.

**NOTE**: Implementing a replay prevention strategy is optional. It is not a requirement
for using this library.  It is, however, *highly* recommended.

The [`QueryAuth\Storage\SignatureStorage`](https://github.com/jeremykendall/query-auth/blob/master/src/QueryAuth/Storage/SignatureStorage.php)
interface is provided to aid in implementing replay attack prevention.

``` php
<?php

namespace QueryAuth\Storage;

interface SignatureStorage
{
    public function exists($key, $signature);

    public function save($key, $signature, $expires);

    public function purge();
}
```

**NOTE**: Implementing the `SignatureStorage` interface is not required to prevent
replay attacks, it's simply present to assist you in implementing the attack
prevention strategy outlined above.

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

## Versions Less Than 3.0+ Deprecated, Not Obsolete

While I'd advise upgrading to v3 as soon as possible, a happy side effect of refactoring
the API without changing the signature creation and validation logic is that
Query Auth 3.0+ is compatible with prior versions of Query Auth. This means that you'll
be able to upgrade Query Auth on the server-side (validation) without needing
to immediately upgrade all client-side (creation) applications. BONUS!

## Installation

Package installation is handled by Composer.

* If you haven't already, please [install Composer](http://getcomposer.org/doc/00-intro.md#installation-nix)
* Create `composer.json` in the root of your project and add query-auth as a dependency:

``` json
{
    "require": {
        "jeremykendall/query-auth": "*"
    }
}
```

* Run `composer install`
* Require Composer's `vendor/autoload.php` script in your bootstrap/init script

## Feedback and Contributions

* Feedback is welcome in the form of pull requests and/or issues.
* Contributions should generally follow the strategy outlined in ["Contributing
  to a project"](https://help.github.com/articles/fork-a-repo#contributing-to-a-project)
* Please submit pull requests against the `develop` branch

## Credits

* Query Auth is my own implementation of the [Signature Version 2
  implementation](https://github.com/aws/aws-sdk-php/blob/master/src/Aws/Common/Signature/SignatureV2.php)
  from the [AWS SDK for PHP 2](https://github.com/aws/aws-sdk-php/blob/master/src/Aws/Common/Signature/SignatureV2.php).
  As such, a version of the Apache License Version 2.0 is included with this
  distribution, and the applicable portion of the AWS SDK for PHP 2 NOTICE file
  is included.

* API key and API secret generation is handled by Anthony Ferrara's
[RandomLib](https://github.com/ircmaxell/RandomLib) random string generator.
