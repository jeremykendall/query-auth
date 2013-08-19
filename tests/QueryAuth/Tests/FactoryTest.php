<?php

namespace QueryAuth\Tests;

use QueryAuth\Factory;
use RandomLib\Factory as RandomFactory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    private $factory;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new Factory();
    }

    protected function tearDown()
    {
        $this->factory = null;
        parent::tearDown();
    }

    public function testFactoryServer()
    {
        $this->assertInstanceOf('QueryAuth\Server', $this->factory->newServer());
    }

    public function testFactoryClient()
    {
        $this->assertInstanceOf('QueryAuth\Client', $this->factory->newClient());
    }

    public function testGetSetRandomFactory()
    {
        // Defaults to RandomLib\Factory
        $this->assertInstanceOf('RandomLib\Factory', $this->factory->getRandomFactory());
        $default = $this->factory->getRandomFactory();
        $new = new RandomFactory();
        $this->factory->setRandomFactory($new);
        $this->assertEquals($default, $this->factory->getRandomFactory());
        $this->assertNotSame($default, $this->factory->getRandomFactory());
    }

    public function testNewKeyGenerator()
    {
        $randomFactory = $this->getMockBuilder('RandomLib\Factory')
            ->getMock();

        $generator = $this->getMockBuilder('RandomLib\Generator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory->setRandomFactory($randomFactory);

        $randomFactory->expects($this->once())
            ->method('getMediumStrengthGenerator')
            ->will($this->returnValue($generator));

        $keyGenerator = $this->factory->newKeyGenerator();
        $this->assertInstanceOf('QueryAuth\KeyGenerator', $keyGenerator);
    }
}
