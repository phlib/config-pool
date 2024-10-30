<?php

namespace Phlib\ConfigPool;

use Phlib\ConfigPool\HashStrategy\HashStrategyInterface;
use Phlib\ConfigPool\HashStrategy\Ordered;

/**
 * @package Phlib\ConfigPool
 */
class ConfigPool
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $calculatedConfig = [];

    /**
     * @var HashStrategyInterface
     */
    protected $hashStrategy;

    public function __construct(array $config, HashStrategyInterface $hashStrategy = null)
    {
        // store the config array for later retrieval
        $this->config = $config;

        if ($hashStrategy === null) {
            // no hasher was provided
            $hashStrategy = new Ordered();
        }

        // setup the hashing
        $this->setHashStrategy($hashStrategy);
    }

    /**
     * Set hash strategy
     *
     * @return $this
     */
    public function setHashStrategy(HashStrategyInterface $hashStrategy)
    {
        // loop the config adding the key as a node
        foreach ($this->config as $key => $value) {
            $hashStrategy->add($key, array_get($value, 'weight', 1));
        }

        $this->hashStrategy = $hashStrategy;

        return $this;
    }

    /**
     * Get config list
     *
     * @param string $key
     * @param int $count
     * @return array
     */
    public function getConfigList($key, $count = 1)
    {
        // find a calcualted config list
        if (!array_key_exists("{$key}.{$count}", $this->calculatedConfig)) {
            // check we aren't storing too many calculated configs
            if (count($this->calculatedConfig) >= 100) {
                // remove the fist in the list, should be the oldest
                array_shift($this->calculatedConfig);
            }

            // get a list of config keys using the count and key provided
            $configList = [];
            foreach ($this->hashStrategy->get($key, $count) as $index) {
                // append the config values to the config list
                $configList[] = $this->config[$index];
            }

            // store for later, a little config cache
            $this->calculatedConfig["{$key}.{$count}"] = $configList;
        }

        return $this->calculatedConfig["{$key}.{$count}"];
    }

    /**
     * Get config
     *
     * @param string $key
     * @return array
     */
    public function getConfig($key)
    {
        // return the first matching config key
        $index = $this->hashStrategy->get($key, 1);

        return $this->config[$index[0]];
    }

    /**
     * Get config all
     *
     * @return array
     */
    public function getOriginalConfig()
    {
        return $this->config;
    }
}
