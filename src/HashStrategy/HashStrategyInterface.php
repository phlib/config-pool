<?php

declare(strict_types=1);

namespace Phlib\ConfigPool\HashStrategy;

/**
 * @package Phlib\ConfigPool
 */
interface HashStrategyInterface
{
    public function add(string $node, int $weight = 1): static;

    public function remove(string $node): static;

    public function get(string $seed, int $count = 1): array;
}
