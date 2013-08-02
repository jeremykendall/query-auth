# Query Auth

Signature generation and validation for REST API query authentication

## API Query Authentication

Most APIs require some sort of query authentication: a method of signing API
requests with an API key and signature.  The signature is usually generated
using a shared secret.  When you're consuming an API, there are (hopefully) easy
to follow steps to create signatures and authenticate your API requests. When
you're writing your own API, you have to whip up both server-side signature
validation and a client-side signature creation strategy. This library endeavors
to handle both of those tasks.

## Usage

There are three components to this library: Client signature creation, server
signature validation, and API key generation.

### Signature Creation

```php
$key = 'API_KEY';
$secret = 'API_SECRET';
$timestamp = time();

$client = new QueryAuth\Client();
$signature = $client->generateSignature($key, $secret, $timestamp);
```

If you plan to pass the signature along via the querystring, make sure to [url
encode](http://php.net/urlencode) it first. A convenience function for
generating a url encoded signature is provided.

```php
$urlEncodedSignature = $client->generateUrlEncodedSignature($key, $secret, $timestamp);
```

### Signature Validation

Grab the API key, timestamp, and signature from the API request, then retrieve
the API secret from whatever persistence layer you're using.  Now validate the
signature.

```php
$key = 'API_KEY_FROM_REQUEST';
$timestamp = 'TIMESTAMP_FROM_REQUEST';
$signature = 'SIGNATURE_FROM_REQUEST';
$secret = 'API_SECRET_FROM_PERSISTENCE_LAYER';

$server = new QueryAuth\Server();
$isValid = $server->validateSignature($key, $secret, $timestamp, $signature);
```

### Key Generation

You can generate API keys and secrets in the following manner.

```php
$randomFactory = new \RandomLib\Factory();
$keyGenerator = new QueryAuth\KeyGenerator($randomFactory);

// 40 character random alphanumeric string
$key = $keyGenerator->generateKey();

// 60 character random string containing the characters
// 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ./
$secret = $keyGenerator->generateSecret();
```

## Installation

Package installation is handled by Composer.

* If you haven't already, please [install Composer](http://getcomposer.org/doc/00-intro.md#installation-nix)
* Create `composer.json` in the root of your project:

```json
{
    "require": {
        "jeremykendall/query-auth": "dev-develop"
    }
}
```

* Run `composer install`
* Require Composer's `vendor/autoload` script in your bootstrap/init script

## Feedback and Contributions

Constructive criticisms and pull-requests are both very welcome.  This is my
first foray into a library like this and I'm sure there are areas that need
improvement.  Feel free to send along any input you've got.
