<?php

declare(strict_types=1);

namespace Phlib\ConfigPool;

use Phlib\ConfigPool\HashStrategy\Ordered;
use PHPUnit\Framework\TestCase;

/**
 * @package Phlib\ConfigPool
 */
class ConfigPoolTest extends TestCase
{
    /**
     * @var array
     */
    private $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = [
            0 => [
                'weight' => 2,
                'host' => 'local1',
                'port' => 123,
                'key1' => 'value1',
            ],
            1 => [
                'weight' => 1,
                'host' => 'local2',
                'port' => 456,
                'key1' => 'value2',
            ],
            2 => [
                'weight' => 3,
                'host' => 'local3',
                'port' => 789,
                'key1' => 'value3',
            ],
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->config = null;
    }

    public function testGetConfigListLevelOne()
    {
        $hashStrategy = $this->createMock(Ordered::class);
        $hashStrategy->expects(static::exactly(3))
            ->method('add');

        $hashStrategy->expects(static::once())
            ->method('get')
            ->with(
                static::equalTo('seed1'),
                static::equalTo(1)
            )
            ->willReturn([0]);

        $poolConfig = new ConfigPool($this->config, $hashStrategy);

        $configList = $poolConfig->getConfigList('seed1');
        static::assertSame(1, count($configList));
        static::assertSame($this->config[0], $configList[0]);
    }

    public function testGetConfigListLevelTwo()
    {
        $poolConfig = new ConfigPool($this->config);

        $configList = $poolConfig->getConfigList('seed1', 2);
        static::assertSame(2, count($configList));
        static::assertSame($this->config[2], $configList[0]);
        static::assertSame($this->config[0], $configList[1]);
    }

    public function testGetOriginalConfig()
    {
        $poolConfig = new ConfigPool($this->config);
        $originalConfig = $poolConfig->getOriginalConfig();
        static::assertSame(count($this->config), count($originalConfig));
        static::assertSame($this->config, $originalConfig);
    }

    public function testGetConfig()
    {
        $poolConfig = new ConfigPool($this->config);
        static::assertSame($this->config[2], $poolConfig->getConfig('seed1'));
        static::assertSame($this->config[2], $poolConfig->getConfig('seed2a'));
    }

    public function testGetConfigWeighted()
    {
        $this->config[0]['weight'] = 1;
        $this->config[1]['weight'] = 0;
        $this->config[2]['weight'] = 0;
        $poolConfig = new ConfigPool($this->config);
        static::assertSame($this->config[0], $poolConfig->getConfig('seed1'));
    }

    /**
     * @large
     */
    public function testGetConfigMany()
    {
        $poolConfig = new ConfigPool($this->config);

        $counter = 200;
        while ($counter--) {
            static::assertSame(1, count($poolConfig->getConfigList(uniqid())));
        }
    }

    /**
     * @large
     */
    public function testGetConfigMany2()
    {
        $poolConfig = new ConfigPool($this->config);

        $counter = 200;
        while ($counter--) {
            static::assertSame(2, count($poolConfig->getConfigList(uniqid(), 2)));
        }
    }
}
