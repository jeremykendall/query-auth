<?php

namespace QueryAuth;

class KeyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KeyGenerator
     */
    private $keyGenerator;

    protected function setUp()
    {
        $factory = new Factory();
        $this->keyGenerator = $factory->newKeyGenerator();
    }

    public function testGenerateKey()
    {
        $key = $this->keyGenerator->generateKey();
        $this->assertRegexp('/^[0-9A-Za-z]{40}$/', $key);
    }

    public function testGenerateSecret()
    {
        $secret = $this->keyGenerator->generateSecret();
        $this->assertRegexp('/^[0-9A-Za-z\/\.\+]{60}$/', $secret);
    }

    public function testGenerateNonce()
    {
        $secret = $this->keyGenerator->generateNonce();
        $this->assertRegexp('/^[0-9A-Za-z\/\.\+]{64}$/', $secret);
    }
}
