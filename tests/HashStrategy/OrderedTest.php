<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

use PHPUnit\Framework\TestCase;

/**
 * @package Phlib\ConfigPool
 */
class OrderedTest extends TestCase
{
    public function testAddReturn()
    {
        $hs = new Ordered();
        static::assertEquals($hs, $hs->add('server1'));
    }

    public function testRemoveReturn()
    {
        $hs = new Ordered();
        static::assertEquals($hs, $hs->remove('server1'));
    }

    public function testGetReturn()
    {
        $hs = new Ordered();
        static::assertEquals([], $hs->get('key1'));
    }

    public function testGetWithData()
    {
        $hs = new Ordered();
        $hs->add('server1');
        static::assertEquals(['server1'], $hs->get('key1'));
    }

    public function testRemoveWithData()
    {
        $hs = new Ordered();
        $hs->add('server1');
        static::assertEquals(['server1'], $hs->get('key1'));
        $hs->remove('server1');
        static::assertEquals([], $hs->get('key1'));
    }

    public function testRemoveWithDataTwo()
    {
        $hs = new Ordered();
        $hs->add('server1');
        $hs->add('server2');

        static::assertCount(2, $hs->get('key1', 2));
        $hs->remove('server1');
        static::assertEquals(['server2'], $hs->get('key1'));
    }

    public function testGetWithDataMax()
    {
        $hs = new Ordered();
        $hs->add('server1');
        $hs->add('server2');
        $hs->add('server3');

        static::assertEquals(3, count($hs->get('key1', 10)));
    }

    public function testGetWeight()
    {
        $hs = new Ordered();
        $hs->add('server1', 1);
        $hs->add('server2', 2);
        $hs->add('server2a', 2);
        $hs->add('server3', 3);

        static::assertEquals(['server3'], $hs->get('test1'));
        static::assertEquals(['server3'], $hs->get('test2'));
        static::assertEquals(['server3', 'server2'], $hs->get('test2', 2));
        static::assertEquals(['server3', 'server2', 'server2a'], $hs->get('test2', 3));
        static::assertEquals(['server3', 'server2', 'server2a', 'server1'], $hs->get('test2', 100));
    }

    public function testGetWeightChange()
    {
        $hs = new Ordered();
        $hs->add('server1', 0);
        $hs->add('server2', 0);
        $hs->add('server3', 1);

        static::assertEquals(['server3'], $hs->get('key1'));

        $hs->remove('server3');
        $hs->add('server3a', 1);
        static::assertEquals(['server3a'], $hs->get('key1'));

        $hs->add('server4', 10);
        static::assertEquals(['server4'], $hs->get('key1'));
        static::assertEquals(['server4', 'server3a'], $hs->get('key1', 2));
    }
}
