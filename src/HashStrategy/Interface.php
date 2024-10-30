<?php

interface Zxm_HashStrategy_Interface
{
    /**
     * Add
     *
     * @param string $node
     * @param int $weight
     * @return Zxm_HashStrategy_Interface
     */
    public function add($node, $weight = 1);

    /**
     * Remove
     *
     * @param string $node
     * @return Zxm_HashStrategy_Interface
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
