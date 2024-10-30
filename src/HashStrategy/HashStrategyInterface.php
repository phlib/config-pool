<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

/**
 * @package Phlib\ConfigPool
 */
interface HashStrategyInterface
{
    /**
     * Add
     *
     * @param string $node
     * @param int $weight
     * @return static
     */
    public function add($node, $weight = 1);

    /**
     * Remove
     *
     * @param string $node
     * @return static
     */
    public function remove($node);

    /**
     * Get
     *
     * @param string $key
     * @param int $count
     * @return array
     */
    public function get($key, $count = 1);
}
