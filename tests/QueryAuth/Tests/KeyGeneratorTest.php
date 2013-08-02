<?php

namespace QueryAuth\Tests;

use QueryAuth\KeyGenerator;
use RandomLib\Factory as RandomFactory;

class KeyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KeyGenerator
     */
    private $keyGenerator;

    protected function setUp()
    {
        $this->keyGenerator = new KeyGenerator(new RandomFactory());
    }

    public function testGenerateKey()
    {
        $key = $this->keyGenerator->generateKey();
        $this->assertRegexp('/^[0-9A-Za-z]{40}$/', $key);
    }

    public function testGenerateSecret()
    {
        $secret = $this->keyGenerator->generateSecret();
        $this->assertRegexp('/^[0-9A-Za-z\/\.]{60}$/', $secret);
    }
}
