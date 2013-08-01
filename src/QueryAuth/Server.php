<?php

namespace QueryAuth;

class Server
{
    /**
     * Is signature valid?
     *
     * @param  string  $key       API key
     * @param  string  $secret    API secret
     * @param  int     $timestamp Unix timestamp
     * @return boolean
     */
    public function validateSignature($key, $secret, $timestamp, $signature)
    {
        $validSignature = \base64_encode(
            \hash_hmac('sha256', $key . $timestamp, $secret, true)
        );

        if ($signature !== $validSignature) {
            return false;
        }

        return true;
    }
}
