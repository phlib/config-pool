<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

use PHPUnit\Framework\TestCase;

/**
 * @package Phlib\ConfigPool
 */
class RandTest extends TestCase
{
    public function testAddReturn()
    {
        $hs = new Rand();
        static::assertEquals($hs, $hs->add('server1'));
    }

    public function testRemoveReturn()
    {
        $hs = new Rand();
        static::assertEquals($hs, $hs->remove('server1'));
    }

    public function testGetReturn()
    {
        $hs = new Rand();
        static::assertEquals([], $hs->get('key1'));
    }

    public function testGetWithData()
    {
        $hs = new Rand();
        $hs->add('server1');
        static::assertEquals(['server1'], $hs->get('key1'));
    }

    public function testRemoveWithData()
    {
        $hs = new Rand();
        $hs->add('server1');
        static::assertEquals(['server1'], $hs->get('key1'));
        $hs->remove('server1');
        static::assertEquals([], $hs->get('key1'));
    }

    public function testRemoveWithDataTwo()
    {
        $hs = new Rand();
        $hs->add('server1');
        $hs->add('server2');

        static::assertCount(2, $hs->get('key1', 2));
        $hs->remove('server1');
        static::assertEquals(['server2'], $hs->get('key1'));
    }

    public function testGetWithDataMax()
    {
        $hs = new Rand();
        $hs->add('server1');
        $hs->add('server2');
        $hs->add('server3');

        static::assertEquals(3, count($hs->get('key1', 10)));
    }

    public function testGetWithRandData()
    {
        $hs = new Rand();
        $nodeList = ['server1', 'server2', 'server3'];
        foreach ($nodeList as $node) {
            $hs->add($node);
        }

        $tries = 0;
        do {
            $nodes = $hs->get(uniqid());
            $idx = array_search($nodes[0], $nodeList);
            if ($idx !== false) {
                unset($nodeList[$idx]);
            }
        } while (count($nodeList) > 0 and $tries++ < 100);

        static::assertLessThan(100, $tries);
    }

    public function testGetWeight()
    {
        $hs = new Rand();
        $hs->add('server1', 5);
        $hs->add('server2', 2);
        $hs->add('server3', 3);

        $counts = [
            'server1' => 0,
            'server2' => 0,
            'server3' => 0,
        ];

        $loops = 1000;
        do {
            $nodes = $hs->get(uniqid());
            $counts[$nodes[0]]++;
        } while ($loops--);

        // assert they are within 10% tolerance
        static::assertEqualsWithDelta(500, $counts['server1'], 100);
        static::assertEqualsWithDelta(200, $counts['server2'], 100);
        static::assertEqualsWithDelta(300, $counts['server3'], 100);
    }

    public function testGetWeightChange()
    {
        $hs = new Rand();
        $hs->add('server1', 0);
        $hs->add('server2', 0);
        $hs->add('server3', 1);

        static::assertEquals(['server3'], $hs->get('key1'));

        $hs->remove('server3');
        $hs->add('server3a', 1);
        static::assertEquals(['server3a'], $hs->get('key1'));
    }
}
