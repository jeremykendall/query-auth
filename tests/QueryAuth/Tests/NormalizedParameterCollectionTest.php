<?php

namespace QueryAuth\Tests;

use QueryAuth\NormalizedParameterCollection;

class NormalizedParameterCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $collection = new NormalizedParameterCollection(
            array(
                'email' => 'sam@example.com',
                'name' => 'Sam Jones',
                'comment' => "Y'all rock!"
            )
        );

        $expected = 'comment=Y%27all%20rock%21&email=sam%40example.com&name=Sam%20Jones';
        $actual = $collection->normalize();

        $this->assertEquals($expected, $actual);
    }

    public function testNormalizeSkipsSignature()
    {
        $collection = new NormalizedParameterCollection(
            array(
                'signature' => 'sig',
                'email' => 'sam@example.com',
                'name' => 'Sam Jones',
                'comment' => "Y'all rock!"
            )
        );

        $expected = 'comment=Y%27all%20rock%21&email=sam%40example.com&name=Sam%20Jones';
        $actual = $collection->normalize();

        $this->assertEquals($expected, $actual);
        // Also ensure 'signature' did not get unset, only skipped
        $this->assertEquals('sig', $collection['signature']);
    }

    public function testImplementsCount()
    {
        $collection = new NormalizedParameterCollection(array('one', 'two', 'three'));
        $this->assertInstanceOf('\Countable', $collection);
        $this->assertEquals(3, count($collection));
    }

    public function testAdd()
    {
        $collection = new NormalizedParameterCollection();
        $collection->add('one', 'uno');
        $collection->add('two', 'dos');

        $this->assertEquals(2, count($collection));
        $this->assertEquals('uno', $collection['one']);
        $this->assertEquals('dos', $collection['two']);
    }

    public function testSetFromArray()
    {
        $data = array('Zaphod' => 'Beeblebrox', 'Arthur' => 'Dent');
        $collection = new NormalizedParameterCollection();
        $collection->setFromArray($data);
        $this->assertEquals($data, $collection->toArray());
    }

    public function testToArray()
    {
        $data = array('Zaphod' => 'Beeblebrox', 'Arthur' => 'Dent');
        $collection = new NormalizedParameterCollection($data);
        $this->assertEquals($data, $collection->toArray());
    }

    public function testImplementsIteratorAggregate()
    {
        $collection = new NormalizedParameterCollection();
        $this->assertInstanceOf('\IteratorAggregate', $collection);
        $this->assertInstanceOf('\ArrayIterator', $collection->getIterator());
    }

    public function testImplementsArrayAccess()
    {
        $collection = new NormalizedParameterCollection();
        $this->assertInstanceOf('\ArrayAccess', $collection);

        $collection->offsetSet('test', 'first');
        $this->assertTrue($collection->offsetExists('test'));
        $this->assertEquals('first', $collection->offsetGet('test'));

        $collection->offsetUnset('test');
        $this->assertFalse($collection->offsetExists('test'));

        $this->assertNull($collection->offsetGet('does-not-exist'));

        $collection->offsetSet(null, 'numeric');
        $this->assertEquals('numeric', $collection->offsetGet(0));
    }
}
