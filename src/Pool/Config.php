<?php

/**
 * Pool Config
 *
 * Used for consistent hasing a pool of configs
 *
 * === Example ===
 * $config = array(
 *      'server1' => array('hostname' => 'localhost', 'port' => 11211),
 *      'server2' => array('hostname' => 'localhost', 'port' => 11212),
 *      'server3' => array('hostname' => 'localhost', 'port' => 11213),
 * );
 * $pool = new Zxm_Pool_Config($config);
 * var_dump($pool->getConfigList('some key', 2));
 *
 * @category    Zxm
 * @package     Zxm_Pool
 * @author      James Dempster (letssurf@gmail.com)
 */
class Zxm_Pool_Config
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
     * @var Zxm_HashStrategy_Interface
     */
    protected $hashStrategy;

    public function __construct(array $config, Zxm_HashStrategy_Interface $hashStrategy = null)
    {
        // store the config array for later retrieval
        $this->config = $config;

        if ($hashStrategy === null) {
            // no hasher was provided
            $hashStrategy = new Zxm_HashStrategy_Ordered();
        }

        // setup the hashing
        $this->setHashStrategy($hashStrategy);
    }

    /**
     * Set hash strategy
     *
     * @return \Zxm_Pool_Config
     */
    public function setHashStrategy(Zxm_HashStrategy_Interface $hashStrategy)
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
