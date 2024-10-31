<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

use PHPUnit\Framework\TestCase;

/**
 * @package Phlib\ConfigPool
 */
class ConsistentTest extends TestCase
{
    public function testAddReturn()
    {
        $pool = new Consistent();
        static::assertSame($pool, $pool->add('server1'));
    }

    public function testRemoveReturn()
    {
        $pool = new Consistent();
        static::assertSame($pool, $pool->remove('server1'));
    }

    public function testGetReturn()
    {
        $pool = new Consistent();
        static::assertSame([], $pool->get('seed1'));
    }

    public function testGetWithData()
    {
        $pool = new Consistent();
        $pool->add('server1');
        static::assertSame(['server1'], $pool->get('seed1'));
    }

    public function testGetWithDataTwo()
    {
        $pool = new Consistent();
        $pool->add('server1');
        $pool->add('server2');
        static::assertSame(['server1'], $pool->get('seed'));
        static::assertSame(['server1', 'server2'], $pool->get('seed', 2));
        static::assertSame(['server2', 'server1'], $pool->get('seed2', 2));
    }

    public function testRemoveWithData()
    {
        $pool = new Consistent();
        $pool->add('server1');
        static::assertSame(['server1'], $pool->get('seed1'));
        $pool->remove('server1');
        static::assertSame([], $pool->get('seed1'));
    }

    public function testRemoveWithDataTwo()
    {
        $pool = new Consistent();
        $pool->add('server1');
        $pool->add('server2');
        static::assertSame(['server1', 'server2'], $pool->get('seed', 2));
        $pool->remove('server1');
        static::assertSame(['server2'], $pool->get('seed'));
    }

    public function testGetWithDataMax()
    {
        $pool = new Consistent();
        $pool->add('server1');
        $pool->add('server2');
        $pool->add('server3');

        static::assertSame(3, count($pool->get('seed1', 10)));
    }

    /**
     * @large
     */
    public function testGetWithRandData()
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

    /**
     * @large
     */
    public function testGetWithRandDataOther()
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

    public function testGetWeight()
    {
        $pool = new Consistent();
        $pool->add('server1', 1);
        $pool->add('server2', 10);
        $pool->add('server3', 1);

        static::assertSame(['server2'], $pool->get('seed1'));
    }

    public function testGetWeightChange()
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
