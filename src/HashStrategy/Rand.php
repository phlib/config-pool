<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

/**
 * @package Phlib\ConfigPool
 */
class Rand implements HashStrategyInterface
{
    private array $nodes = [];

    private array $weightedList = [];

    public function add(string $node, int $weight = 1): static
    {
        if (!in_array($node, $this->nodes, true)) {
            // add the node to the nodes array
            $this->nodes[] = $node;

            while ($weight-- > 0) {
                $this->weightedList[] = $node;
            }
        }

        return $this;
    }

    public function remove(string $node): static
    {
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

    public function get(string $seed, int $count = 1): array
    {
        $weightedList = $this->weightedList;

        shuffle($weightedList);
        $weightedList = array_unique($weightedList);

        return array_slice($weightedList, 0, $count);
    }
}
