<?php

declare(strict_types=1);

namespace Phlib\ConfigPool;

use Phlib\ConfigPool\HashStrategy\Ordered;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @package Phlib\ConfigPool
 */
class ConfigPoolTest extends TestCase
{
    public static function dataConfig(): array
    {
        $config = [
            'host1' => [
                'weight' => 2,
                'host' => 'local1',
                'port' => 123,
                'key1' => 'value1',
            ],
            'host2' => [
                'weight' => 1,
                'host' => 'local2',
                'port' => 456,
                'key1' => 'value2',
            ],
            'host3' => [
                'weight' => 3,
                'host' => 'local3',
                'port' => 789,
                'key1' => 'value3',
            ],
        ];

        return [
            'index' => [array_values($config)],
            'assoc' => [$config],
        ];
    }

    #[DataProvider('dataConfig')]
    public function testGetConfigListLevelOne(array $config): void
    {
        $hashStrategy = $this->createMock(Ordered::class);
        $hashStrategy->expects(static::exactly(3))
            ->method('add');

        $firstKey = array_key_first($config);
        $hashStrategy->expects(static::once())
            ->method('get')
            ->with(
                static::equalTo('seed1'),
                static::equalTo(1)
            )
            ->willReturn([$firstKey]);

        $poolConfig = new ConfigPool($config, $hashStrategy);

        $configList = $poolConfig->getConfigList('seed1');

        static::assertSame(1, count($configList));
        static::assertSame($config[$firstKey], $configList[0]);
    }

    #[DataProvider('dataConfig')]
    public function testGetConfigListLevelTwo(array $config): void
    {
        $poolConfig = new ConfigPool($config);

        $configList = $poolConfig->getConfigList('seed1', 2);

        $firstKey = array_key_first($config);
        $lastKey = array_key_last($config);
        static::assertSame(2, count($configList));
        static::assertSame($config[$lastKey], $configList[0]);
        static::assertSame($config[$firstKey], $configList[1]);
    }

    #[DataProvider('dataConfig')]
    public function testGetOriginalConfig(array $config): void
    {
        $poolConfig = new ConfigPool($config);
        $originalConfig = $poolConfig->getOriginalConfig();

        static::assertSame(count($config), count($originalConfig));
        static::assertSame($config, $originalConfig);
    }

    #[DataProvider('dataConfig')]
    public function testGetConfig(array $config): void
    {
        $poolConfig = new ConfigPool($config);

        $lastKey = array_key_last($config);
        static::assertSame($config[$lastKey], $poolConfig->getConfig('seed1'));
        static::assertSame($config[$lastKey], $poolConfig->getConfig('seed2a'));
    }

    #[DataProvider('dataConfig')]
    public function testGetConfigWeighted(array $config): void
    {
        // Reset weights
        foreach ($config as &$item) {
            $item['weight'] = 0;
        }
        unset($item);
        $firstKey = array_key_first($config);
        $config[$firstKey]['weight'] = 1;

        $poolConfig = new ConfigPool($config);

        static::assertSame($config[$firstKey], $poolConfig->getConfig('seed1'));
    }

    #[DataProvider('dataConfig')]
    public function testGetConfigMany(array $config): void
    {
        $poolConfig = new ConfigPool($config);

        $counter = 200;
        while ($counter--) {
            static::assertSame(1, count($poolConfig->getConfigList(uniqid())));
        }
    }

    #[DataProvider('dataConfig')]
    public function testGetConfigMany2(array $config): void
    {
        $poolConfig = new ConfigPool($config);

        $counter = 200;
        while ($counter--) {
            static::assertSame(2, count($poolConfig->getConfigList(uniqid(), 2)));
        }
    }
}
