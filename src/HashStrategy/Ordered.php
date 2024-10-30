<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

/**
 * @package Phlib\ConfigPool
 */
class Ordered implements HashStrategyInterface
{
    /**
     * @var array
     */
    private $nodes = [];

    /**
     * @var int
     */
    private $counter = 1000;

    /**
     * @var bool
     */
    private $sorted = false;

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

        if (!in_array($node, $this->nodes, true)) {
            // add the node to the nodes array
            if ($weight) {
                $nodeIndex = 'w' .
                    str_pad((string)$weight, 3, '0', STR_PAD_LEFT) .
                    '.' .
                    str_pad((string)--$this->counter, 3, '0', STR_PAD_LEFT);

                $this->nodes[$nodeIndex] = $node;
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

        $nodeIndex = array_search($node, $this->nodes, true);
        if ($nodeIndex !== false) {
            // remove the found node
            unset($this->nodes[$nodeIndex]);
        }

        return $this;
    }

    /**
     * Get
     *
     * @param string $seed
     * @param int $count
     * @return array
     */
    public function get($seed, $count = 1)
    {
        $seed = (string)$seed;
        $count = (int)$count;

        if (!$this->sorted) {
            krsort($this->nodes, SORT_STRING);
            $this->sorted = true;
        }

        return array_slice(array_values($this->nodes), 0, $count);
    }
}
