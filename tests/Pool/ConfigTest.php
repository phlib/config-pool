<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class Zxm_Pool_ConfigTest extends TestCase
{
    /**
     * @var array
     */
    protected $config;

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
        $hashStrategy = $this->createMock(Zxm_HashStrategy_Ordered::class);
        $hashStrategy->expects(static::exactly(3))
            ->method('add');

        $hashStrategy->expects(static::once())
            ->method('get')
            ->with(
                static::equalTo('key1'),
                static::equalTo(1)
            )
            ->willReturn([0]);

        $poolConfig = new Zxm_Pool_Config($this->config, $hashStrategy);

        $configList = $poolConfig->getConfigList('key1');
        static::assertEquals(1, count($configList));
        static::assertEquals($this->config[0], $configList[0]);
    }

    public function testGetConfigListLevelTwo()
    {
        $poolConfig = new Zxm_Pool_Config($this->config);

        $configList = $poolConfig->getConfigList('key1', 2);
        static::assertEquals(2, count($configList));
        static::assertEquals($this->config[2], $configList[0]);
        static::assertEquals($this->config[0], $configList[1]);
    }

    public function testGetOriginalConfig()
    {
        $poolConfig = new Zxm_Pool_Config($this->config);
        $originalConfig = $poolConfig->getOriginalConfig();
        static::assertEquals(count($this->config), count($originalConfig));
        static::assertEquals($this->config, $originalConfig);
    }

    public function testGetConfig()
    {
        $poolConfig = new Zxm_Pool_Config($this->config);
        static::assertEquals($this->config[2], $poolConfig->getConfig('key1'));
        static::assertEquals($this->config[2], $poolConfig->getConfig('key2a'));
    }

    public function testGetConfigWeighted()
    {
        $this->config[0]['weight'] = 1;
        $this->config[1]['weight'] = 0;
        $this->config[2]['weight'] = 0;
        $poolConfig = new Zxm_Pool_Config($this->config);
        static::assertEquals($this->config[0], $poolConfig->getConfig('key1'));
    }

    /**
     * @large
     */
    public function testGetConfigMany()
    {
        $poolConfig = new Zxm_Pool_Config($this->config);

        $counter = 200;
        while ($counter--) {
            static::assertEquals(1, count($poolConfig->getConfigList(uniqid())));
        }
    }

    /**
     * @large
     */
    public function testGetConfigMany2()
    {
        $poolConfig = new Zxm_Pool_Config($this->config);

        $counter = 200;
        while ($counter--) {
            static::assertEquals(2, count($poolConfig->getConfigList(uniqid(), 2)));
        }
    }
}
