<?php

namespace QueryAuth\Request\Adapter\Incoming;

class SlimRequestAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $adapter;
    protected $adaptee;

    protected function setUp()
    {
        parent::setUp();

        $this->adaptee = $this->getMockBuilder('Slim\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapter = new SlimRequestAdapter($this->adaptee);
    }

    protected function tearDown()
    {
        $this->adapter = null;

        parent::tearDown();
    }

    public function testGetMethod()
    {
        $this->adaptee->method('getMethod')
            ->willReturn('GET');

        $actual = $this->adapter->getMethod();

        $this->assertEquals('GET', $actual);
    }

    public function testGetHost()
    {
        $this->adaptee->method('getHost')
            ->willReturn('www.example.com');

        $actual = $this->adapter->getHost();

        $this->assertEquals('www.example.com', $actual);
    }

    public function testGetPath()
    {
        $this->adaptee->method('getPath')
            ->willReturn('/index.php');

        $actual = $this->adapter->getPath();

        $this->assertEquals('/index.php', $actual);
    }

    public function testGetParamsPost()
    {
        $expected = ['one' => 'two'];

        $adaptee = $this->getMockBuilder('Slim\Http\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = new SlimRequestAdapter($adaptee);

        $adaptee->method('getMethod')
            ->willReturn('POST');

        $adaptee->method('post')
            ->willReturn($expected);

        $params = $adapter->getParams();

        $this->assertEquals($expected, $params);
    }

    public function testGetParamsNotPost()
    {
        $expected = ['one' => 'two'];

        $this->adaptee->method('getMethod')
            ->willReturn('GET');

        $this->adaptee->method('get')
            ->willReturn($expected);

        $params = $this->adapter->getParams();

        $this->assertEquals($expected, $params);
    }

    /**
     * @group 18
     */
    public function testGetQueryStringParametersDeleteRequest()
    {
        $expected = ['one' => 'two'];

        $this->adaptee->method('getMethod')
            ->willReturn('DELETE');

        $this->adaptee->method('params')
            ->willReturn($expected);

        $params = $this->adapter->getParams();

        $this->assertEquals($expected, $params);
    }
}
