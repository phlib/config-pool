<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

/**
 * @package Phlib\ConfigPool
 */
class Ordered implements HashStrategyInterface
{
    private array $nodes = [];

    private int $counter = 1000;

    private bool $sorted = false;

    public function add(string $node, int $weight = 1): static
    {
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

    public function remove(string $node): static
    {
        $nodeIndex = array_search($node, $this->nodes, true);
        if ($nodeIndex !== false) {
            // remove the found node
            unset($this->nodes[$nodeIndex]);
        }

        return $this;
    }

    public function get(string $seed, int $count = 1): array
    {
        if (!$this->sorted) {
            krsort($this->nodes, SORT_STRING);
            $this->sorted = true;
        }

        return array_slice(array_values($this->nodes), 0, $count);
    }
}
