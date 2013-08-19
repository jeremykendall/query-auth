<?php

namespace QueryAuth\Tests;

use QueryAuth\ParameterCollection;

class ParameterCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $collection = new ParameterCollection(
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
        $collection = new ParameterCollection(
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
        $collection = new ParameterCollection(array('one', 'two', 'three'));
        $this->assertInstanceOf('\Countable', $collection);
        $this->assertEquals(3, count($collection));
    }

    public function testSet()
    {
        $collection = new ParameterCollection();
        $collection->set('one', 'uno');
        $collection->set('two', 'dos');

        $this->assertEquals(2, count($collection));
        $this->assertEquals('uno', $collection['one']);
        $this->assertEquals('dos', $collection['two']);
    }

    public function testSetFromArray()
    {
        $data = array('Zaphod' => 'Beeblebrox', 'Arthur' => 'Dent');
        $collection = new ParameterCollection();
        $collection->setFromArray($data);
        $this->assertEquals($data, $collection->toArray());
    }

    public function testToArray()
    {
        $data = array('Zaphod' => 'Beeblebrox', 'Arthur' => 'Dent');
        $collection = new ParameterCollection($data);
        $this->assertEquals($data, $collection->toArray());
    }

    public function testImplementsIteratorAggregate()
    {
        $collection = new ParameterCollection();
        $this->assertInstanceOf('\IteratorAggregate', $collection);
        $this->assertInstanceOf('\ArrayIterator', $collection->getIterator());
    }

    public function testImplementsArrayAccess()
    {
        $collection = new ParameterCollection();
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
