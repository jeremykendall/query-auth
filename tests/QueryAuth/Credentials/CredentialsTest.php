<?php

namespace QueryAuth\Credentials;

use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    public function testCredentials()
    {
        $key = 'not so secret API key';
        $secret = 'super secret API secret';

        $credentials = new Credentials($key, $secret);

        $this->assertInstanceOf(
            'QueryAuth\Credentials\CredentialsInterface',
            $credentials
        );
        $this->assertEquals($key, $credentials->getKey());
        $this->assertEquals($secret, $credentials->getSecret());
    }
}
