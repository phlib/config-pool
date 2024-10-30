<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class Zxm_HashStrategy_ConsistentTest extends TestCase
{
    public function testAddReturn()
    {
        $pool = new Zxm_HashStrategy_Consistent();
        static::assertEquals($pool, $pool->add('server1'));
    }

    public function testRemoveReturn()
    {
        $pool = new Zxm_HashStrategy_Consistent();
        static::assertEquals($pool, $pool->remove('server1'));
    }

    public function testGetReturn()
    {
        $pool = new Zxm_HashStrategy_Consistent();
        static::assertEquals([], $pool->get('key1'));
    }

    public function testGetWithData()
    {
        $pool = new Zxm_HashStrategy_Consistent();
        $pool->add('server1');
        static::assertEquals(['server1'], $pool->get('key1'));
    }

    public function testGetWithDataTwo()
    {
        $pool = new Zxm_HashStrategy_Consistent();
        $pool->add('server1');
        $pool->add('server2');
        static::assertEquals(['server1'], $pool->get('key1'));
        static::assertEquals(['server1', 'server2'], $pool->get('key1', 2));
        static::assertEquals(['server2', 'server1'], $pool->get('key2abc', 2));
    }

    public function testRemoveWithData()
    {
        $pool = new Zxm_HashStrategy_Consistent();
        $pool->add('server1');
        static::assertEquals(['server1'], $pool->get('key1'));
        $pool->remove('server1');
        static::assertEquals([], $pool->get('key1'));
    }

    public function testRemoveWithDataTwo()
    {
        $pool = new Zxm_HashStrategy_Consistent();
        $pool->add('server1');
        $pool->add('server2');
        static::assertEquals(['server1', 'server2'], $pool->get('key1', 2));
        $pool->remove('server1');
        static::assertEquals(['server2'], $pool->get('key1'));
    }

    public function testGetWithDataMax()
    {
        $pool = new Zxm_HashStrategy_Consistent();
        $pool->add('server1');
        $pool->add('server2');
        $pool->add('server3');

        static::assertEquals(3, count($pool->get('key1', 10)));
    }

    /**
     * @large
     */
    public function testGetWithRandData()
    {
        $pool = new Zxm_HashStrategy_Consistent();
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
        static::assertEquals($expected, $actual);
    }

    /**
     * @large
     */
    public function testGetWithRandDataOther()
    {
        $pool = new Zxm_HashStrategy_Consistent();
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
        static::assertEquals($expected, $actual);
    }

    public function testGetWeight()
    {
        $pool = new Zxm_HashStrategy_Consistent();
        $pool->add('server1', 1);
        $pool->add('server2', 10);
        $pool->add('server3', 1);

        static::assertEquals(['server2'], $pool->get('key1'));
    }

    public function testGetWeightChange()
    {
        $pool = new Zxm_HashStrategy_Consistent();
        $pool->add('server1', 1);
        $pool->add('server2', 10);
        $pool->add('server3', 1);

        static::assertEquals(['server2'], $pool->get('key1'));

        $pool->add('server4', 100);
        static::assertEquals(['server4'], $pool->get('key1'));
    }
}
