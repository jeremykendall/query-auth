<?php

namespace QueryAuth\Tests;

use QueryAuth\Factory;
use RandomLib\Factory as RandomFactory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    private $factory;
    private $randomFactory;
    private $generator;

    protected function setUp()
    {
        parent::setUp();
        $this->factory = new Factory();
        $this->randomFactory = $this->getMockBuilder('RandomLib\Factory')
            ->getMock();
        $this->generator = $this->getMockBuilder('RandomLib\Generator')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        $this->factory = null;
        parent::tearDown();
    }

    public function testFactoryServer()
    {
        $this->assertInstanceOf('QueryAuth\Server', $this->factory->getServer());
    }

    public function testFactoryClient()
    {
        $this->assertInstanceOf('QueryAuth\Client', $this->factory->getClient());
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

    public function testGetLowStrengthKeyGenerator()
    {
        $this->factory->setRandomFactory($this->randomFactory);

        $this->randomFactory->expects($this->once())
            ->method('getLowStrengthGenerator')
            ->will($this->returnValue($this->generator));

        $keyGenerator = $this->factory->getLowStrengthKeyGenerator();
        $this->assertInstanceOf('QueryAuth\KeyGenerator', $keyGenerator);
    }

    public function testGetMediumStrengthKeyGenerator()
    {
        $this->factory->setRandomFactory($this->randomFactory);

        $this->randomFactory->expects($this->once())
            ->method('getMediumStrengthGenerator')
            ->will($this->returnValue($this->generator));

        $keyGenerator = $this->factory->getMediumStrengthKeyGenerator();
        $this->assertInstanceOf('QueryAuth\KeyGenerator', $keyGenerator);
    }

    protected function getMockRandomFactory()
    {
        return $this->getMockBuilder('RandomLib\Factory')->getMock();
    }

    protected function getMockGenerator()
    {
        return $this->getMockBuilder('RandomLib\Generator')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
