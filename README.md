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

There are three components to this library: Server signature validation, client
signature creation, and API key generation.

### Query Auth Overview

Given an API key, API secret, and a Unix timestamp, you can generate a signature
by Base64 encoding a SHA-256 hash of API key concatenated with the Unix timestamp
and the API secret.

Validating the signature is as easy as recreating the signature on the server
side and comparing it to the signature provided in the API request.  As long as
each API request contains the API key, a Unix timestamp, and the signature,
it's trivial to recreate the signature for the purposes of authenticating the
request.

### Client Signature Creation

```php
$key = 'API_KEY';
$secret = 'API_SECRET';
$timestamp = time();

$client = new QueryAuth\Client(); 
$signature = $client->generateSignature($key, $secret, $timestamp);
```

If you plan to pass the signature along via the querystring, make sure to
`urlencode` it first. A convenience function for generating a url encoded
signature is provided.

```php
$urlEncodedSignature = $client->generateUrlEncodedSignature($key, $secret, $timestamp);
```

### Server Signature Validation

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

**NOTE**: *This is extremely naive key and secret generation. Use at your own risk.*

You can generate API keys and secrets in the following manner.

```php
$keyGenerator = new QueryAuth\KeyGenerator();

// 32 character alphanumeric string
$key = $keyGenerator->generateKey();

// 44 character alphanumeric string
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
