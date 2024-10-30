<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

/**
 * @package Phlib\ConfigPool
 */
class Rand implements HashStrategyInterface
{
    /**
     * @var array
     */
    private $nodes = [];

    /**
     * @var array
     */
    private $weightedList = [];

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
            $this->nodes[] = $node;

            while ($weight-- > 0) {
                $this->weightedList[] = $node;
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

            // loop the weighted list removing the nodes
            foreach ($this->weightedList as $idx => $listNode) {
                // then remove it
                if ($listNode === $node) {
                    unset($this->weightedList[$idx]);
                }
            }
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

        $weightedList = $this->weightedList;

        shuffle($weightedList);
        $weightedList = array_unique($weightedList);

        return array_slice($weightedList, 0, $count);
    }
}
