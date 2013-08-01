<?php

namespace QueryAuth;

class KeyGenerator
{
    /**
     * Returns 32 character alphanumeric randomish string
     *
     * @return string API key
     */
    public function generateKey()
    {
        $serial = sha1(uniqid(rand(), true));
        $checksum = substr(md5($serial), 0, 4);

        return sha1(uniqid(rand(), true)) . substr(md5($serial), 0, 4);
    }

    /**
     * Returns 44 character alphanumeric randomish string
     *
     * @return string API secret
     */
    public function generateSecret()
    {
        return hash_hmac('sha256', sha1(time()), microtime());
    }
}
