<?php

declare(strict_types=1);

namespace Phlib\ConfigPool;

use Phlib\ConfigPool\HashStrategy\HashStrategyInterface;
use Phlib\ConfigPool\HashStrategy\Ordered;

/**
 * @package Phlib\ConfigPool
 */
class ConfigPool
{
    private array $config;

    private array $calculatedConfig = [];

    private HashStrategyInterface $hashStrategy;

    public function __construct(
        array $config,
        HashStrategyInterface $hashStrategy = null,
    ) {
        // store the config array for later retrieval
        $this->config = $config;

        if ($hashStrategy === null) {
            // no hasher was provided
            $hashStrategy = new Ordered();
        }
        $this->hashStrategy = $hashStrategy;

        $this->initHashStrategy();
    }

    private function initHashStrategy(): void
    {
        // loop the config adding the key as a node
        foreach ($this->config as $key => $value) {
            $weight = $value['weight'] ?? 1;
            $this->hashStrategy->add((string)$key, $weight);
        }
    }

    public function getConfigList(string $seed, int $count = 1): array
    {
        // find a calculated config list
        if (!array_key_exists("{$seed}.{$count}", $this->calculatedConfig)) {
            // check we aren't storing too many calculated configs
            if (count($this->calculatedConfig) >= 100) {
                // remove the fist in the list, should be the oldest
                array_shift($this->calculatedConfig);
            }

            // get a list of config keys using the count and seed provided
            $configList = [];
            foreach ($this->hashStrategy->get($seed, $count) as $index) {
                // append the config values to the config list
                $configList[] = $this->config[$index];
            }

            // store for later, a little config cache
            $this->calculatedConfig["{$seed}.{$count}"] = $configList;
        }

        return $this->calculatedConfig["{$seed}.{$count}"];
    }

    public function getConfig(string $seed): array
    {
        // return the first matching config key
        $index = $this->hashStrategy->get($seed, 1);

        return $this->config[$index[0]];
    }

    public function getOriginalConfig(): array
    {
        return $this->config;
    }
}
