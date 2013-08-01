<?php

namespace QueryAuth;

class Client
{
    /**
     * Generate signature
     *
     * @param  string $key       API key
     * @param  string $secret    API secret
     * @param  int    $timestamp Unix timestamp
     * @return string Signature
     */
    public function generateSignature($key, $secret, $timestamp)
    {
        return \base64_encode(
            \hash_hmac('sha256', $key . $timestamp, $secret, true)
        );
    }

    /**
     * Generate URL encoded signature
     *
     * @param  string $key       API key
     * @param  string $secret    API secret
     * @param  int    $timestamp Unix timestamp
     * @return string Signature
     */
    public function generateUrlEncodedSignature($key, $secret, $timestamp)
    {
        return urlencode($this->generateSignature($key, $secret, $timestamp));
    }
}
