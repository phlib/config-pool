<?php

namespace Phlib\ConfigPool\HashStrategy;

/**
 * @package Phlib\ConfigPool
 */
class Ordered implements HashStrategyInterface
{
    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * @var int
     */
    protected $counter = 1000;

    /**
     * @var bool
     */
    protected $sorted = false;

    /**
     * Add
     *
     * @param string $node
     * @param int $weight
     * @return static
     */
    public function add($node, $weight = 1)
    {
        $node = (string)$node;
        $weight = (int)$weight;

        if (!in_array($node, $this->nodes)) {
            // add the node to the nodes array
            if ($weight) {
                $key = 'w'
                    . str_pad($weight, 3, 0, STR_PAD_LEFT)
                    . '.'
                    . str_pad(--$this->counter, 3, 0, STR_PAD_LEFT);

                $this->nodes[$key] = $node;
                $this->sorted = false;
            }
        }

        return $this;
    }

    /**
     * Remove
     *
     * @param string $node
     * @return static
     */
    public function remove($node)
    {
        $node = (string)$node;

        $nodeIndex = array_search($node, $this->nodes);
        if ($nodeIndex !== false) {
            // remove the found node
            unset($this->nodes[$nodeIndex]);
        }

        return $this;
    }

    /**
     * Get
     *
     * @param string $key
     * @param int $count
     * @return array
     */
    public function get($key, $count = 1)
    {
        $key = (string)$key;
        $count = (int)$count;

        if (!$this->sorted) {
            krsort($this->nodes, SORT_STRING);
            $this->sorted = true;
        }

        return array_slice(array_values($this->nodes), 0, $count);
    }
}
