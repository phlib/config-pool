<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

use PHPUnit\Framework\TestCase;

/**
 * @package Phlib\ConfigPool
 */
class RandTest extends TestCase
{
    public function testAddReturn(): void
    {
        $hs = new Rand();
        static::assertSame($hs, $hs->add('server1'));
    }

    public function testRemoveReturn(): void
    {
        $hs = new Rand();
        static::assertSame($hs, $hs->remove('server1'));
    }

    public function testGetReturn(): void
    {
        $hs = new Rand();
        static::assertSame([], $hs->get('seed1'));
    }

    public function testGetWithData(): void
    {
        $hs = new Rand();
        $hs->add('server1');
        static::assertSame(['server1'], $hs->get('seed1'));
    }

    public function testRemoveWithData(): void
    {
        $hs = new Rand();
        $hs->add('server1');
        static::assertSame(['server1'], $hs->get('seed1'));
        $hs->remove('server1');
        static::assertSame([], $hs->get('seed1'));
    }

    public function testRemoveWithDataTwo(): void
    {
        $hs = new Rand();
        $hs->add('server1');
        $hs->add('server2');

        static::assertCount(2, $hs->get('seed1', 2));
        $hs->remove('server1');
        static::assertSame(['server2'], $hs->get('seed1'));
    }

    public function testGetWithDataMax(): void
    {
        $hs = new Rand();
        $hs->add('server1');
        $hs->add('server2');
        $hs->add('server3');

        static::assertSame(3, count($hs->get('seed1', 10)));
    }

    public function testGetWithRandData(): void
    {
        $hs = new Rand();
        $nodeList = ['server1', 'server2', 'server3'];
        foreach ($nodeList as $node) {
            $hs->add($node);
        }

        $tries = 0;
        do {
            $nodes = $hs->get(uniqid());
            $idx = array_search($nodes[0], $nodeList, true);
            if ($idx !== false) {
                unset($nodeList[$idx]);
            }
        } while (count($nodeList) > 0 and $tries++ < 100);

        static::assertLessThan(100, $tries);
    }

    public function testGetWeight(): void
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

    public function testGetWeightChange(): void
    {
        $hs = new Rand();
        $hs->add('server1', 0);
        $hs->add('server2', 0);
        $hs->add('server3', 1);

        static::assertSame(['server3'], $hs->get('seed1'));

        $hs->remove('server3');
        $hs->add('server3a', 1);
        static::assertSame(['server3a'], $hs->get('seed1'));
    }
}
