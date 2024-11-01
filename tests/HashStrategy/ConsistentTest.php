<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @package Phlib\ConfigPool
 */
class ConsistentTest extends TestCase
{
    public static function dataHashType(): array
    {
        return [
            'crc32' => ['crc32'],
            'md5' => ['md5'],
        ];
    }

    #[DataProvider('dataHashType')]
    public function testAddReturn(string $hashType): void
    {
        $pool = new Consistent($hashType);
        static::assertSame($pool, $pool->add('server1'));
    }

    #[DataProvider('dataHashType')]
    public function testRemoveReturn(string $hashType): void
    {
        $pool = new Consistent($hashType);
        static::assertSame($pool, $pool->remove('server1'));
    }

    #[DataProvider('dataHashType')]
    public function testGetReturn(string $hashType): void
    {
        $pool = new Consistent($hashType);
        static::assertSame([], $pool->get('seed1'));
    }

    #[DataProvider('dataHashType')]
    public function testGetWithData(string $hashType): void
    {
        $pool = new Consistent($hashType);
        $pool->add('server1');
        static::assertSame(['server1'], $pool->get('seed1'));
    }

    public static function dataHashTypeDataTwo(): array
    {
        return [
            'crc32' => [
                'hashType' => 'crc32',
                'expectedSeedA1' => ['server1'],
                'expectedSeedA2' => ['server1', 'server2'],
                'expectedSeedB2' => ['server2', 'server1'],
            ],
            'md5' => [
                'hashType' => 'md5',
                'expectedSeedA1' => ['server2'],
                'expectedSeedA2' => ['server2', 'server1'],
                'expectedSeedB2' => ['server1', 'server2'],
            ],
        ];
    }

    #[DataProvider('dataHashTypeDataTwo')]
    public function testGetWithDataTwo(
        string $hashType,
        array $expectedSeedA1,
        array $expectedSeedA2,
        array $expectedSeedB2,
    ): void {
        $pool = new Consistent($hashType);
        $pool->add('server1');
        $pool->add('server2');

        static::assertSame($expectedSeedA1, $pool->get('seed'));
        static::assertSame($expectedSeedA2, $pool->get('seed', 2));
        static::assertSame($expectedSeedB2, $pool->get('seed2', 2));
    }

    #[DataProvider('dataHashType')]
    public function testRemoveWithData(string $hashType): void
    {
        $pool = new Consistent($hashType);
        $pool->add('server1');
        static::assertSame(['server1'], $pool->get('seed1'));
        $pool->remove('server1');
        static::assertSame([], $pool->get('seed1'));
    }

    #[DataProvider('dataHashTypeDataTwo')]
    public function testRemoveWithDataTwo(
        string $hashType,
        array $expectedSeedA1,
        array $expectedSeedA2,
        array $expectedSeedB2,
    ): void {
        $pool = new Consistent($hashType);
        $pool->add('server1');
        $pool->add('server2');
        static::assertSame($expectedSeedA2, $pool->get('seed', 2));
        $pool->remove('server1');
        static::assertSame(['server2'], $pool->get('seed'));
    }

    #[DataProvider('dataHashType')]
    public function testGetWithDataMax(string $hashType): void
    {
        $pool = new Consistent($hashType);
        $pool->add('server1');
        $pool->add('server2');
        $pool->add('server3');

        static::assertSame(3, count($pool->get('seed1', 10)));
    }

    #[DataProvider('dataHashType')]
    public function testGetWithRandData(string $hashType): void
    {
        $pool = new Consistent($hashType);
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

    #[DataProvider('dataHashType')]
    public function testGetWithRandDataOther(string $hashType): void
    {
        $pool = new Consistent($hashType);
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

    #[DataProvider('dataHashType')]
    public function testGetWeight(string $hashType): void
    {
        $pool = new Consistent($hashType);
        $pool->add('server1', 1);
        $pool->add('server2', 10);
        $pool->add('server3', 1);

        static::assertSame(['server2'], $pool->get('seed1'));
    }

    #[DataProvider('dataHashType')]
    public function testGetWeightChange(string $hashType): void
    {
        $pool = new Consistent($hashType);
        $pool->add('server1', 1);
        $pool->add('server2', 10);
        $pool->add('server3', 1);

        static::assertSame(['server2'], $pool->get('seed1'));

        $pool->add('server4', 100);
        static::assertSame(['server4'], $pool->get('seed1'));
    }
}
