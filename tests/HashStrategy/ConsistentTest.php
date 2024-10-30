<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

use PHPUnit\Framework\TestCase;

/**
 * @package Phlib\ConfigPool
 */
class ConsistentTest extends TestCase
{
    public function testAddReturn(): void
    {
        $pool = new Consistent();
        static::assertSame($pool, $pool->add('server1'));
    }

    public function testRemoveReturn(): void
    {
        $pool = new Consistent();
        static::assertSame($pool, $pool->remove('server1'));
    }

    public function testGetReturn(): void
    {
        $pool = new Consistent();
        static::assertSame([], $pool->get('seed1'));
    }

    public function testGetWithData(): void
    {
        $pool = new Consistent();
        $pool->add('server1');
        static::assertSame(['server1'], $pool->get('seed1'));
    }

    public function testGetWithDataTwo(): void
    {
        $pool = new Consistent();
        $pool->add('server1');
        $pool->add('server2');
        static::assertSame(['server1'], $pool->get('seed'));
        static::assertSame(['server1', 'server2'], $pool->get('seed', 2));
        static::assertSame(['server2', 'server1'], $pool->get('seed2', 2));
    }

    public function testRemoveWithData(): void
    {
        $pool = new Consistent();
        $pool->add('server1');
        static::assertSame(['server1'], $pool->get('seed1'));
        $pool->remove('server1');
        static::assertSame([], $pool->get('seed1'));
    }

    public function testRemoveWithDataTwo(): void
    {
        $pool = new Consistent();
        $pool->add('server1');
        $pool->add('server2');
        static::assertSame(['server1', 'server2'], $pool->get('seed', 2));
        $pool->remove('server1');
        static::assertSame(['server2'], $pool->get('seed'));
    }

    public function testGetWithDataMax(): void
    {
        $pool = new Consistent();
        $pool->add('server1');
        $pool->add('server2');
        $pool->add('server3');

        static::assertSame(3, count($pool->get('seed1', 10)));
    }

    public function testGetWithRandData(): void
    {
        $pool = new Consistent();
        $pool->add('server1');
        $pool->add('server2');
        $pool->add('server3');

        $count = 200;
        $expected = 2;
        $actual = null;
        while ($count--) {
            $actual = count($pool->get(uniqid(), 2));
            if ($actual !== $expected) {
                break;
            }
        }
        static::assertSame($expected, $actual);
    }

    public function testGetWithRandDataOther(): void
    {
        $pool = new Consistent();
        $pool->add('server1');
        $pool->add('server2');
        $pool->add('server3');

        $count = 200;
        $expected = 3;
        $actual = null;
        while ($count--) {
            $actual = count($pool->get(uniqid(), 10));
            if ($actual !== $expected) {
                break;
            }
        }
        static::assertSame($expected, $actual);
    }

    public function testGetWeight(): void
    {
        $pool = new Consistent();
        $pool->add('server1', 1);
        $pool->add('server2', 10);
        $pool->add('server3', 1);

        static::assertSame(['server2'], $pool->get('seed1'));
    }

    public function testGetWeightChange(): void
    {
        $pool = new Consistent();
        $pool->add('server1', 1);
        $pool->add('server2', 10);
        $pool->add('server3', 1);

        static::assertSame(['server2'], $pool->get('seed1'));

        $pool->add('server4', 100);
        static::assertSame(['server4'], $pool->get('seed1'));
    }
}
