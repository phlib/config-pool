<?php

namespace Phlib\ConfigPool\HashStrategy;

/**
 * @package Phlib\ConfigPool
 */
class Consistent implements HashStrategyInterface
{
    /**
     * @var int
     */
    protected $replicas = 64;

    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * @var array
     */
    protected $circle = [];

    /**
     * @var array
     */
    protected $positions = [];

    /**
     * @var string
     */
    protected $hashType = 'crc32';

    /**
     * @param string $hashType
     */
    public function __construct($hashType = 'crc32')
    {
        $availableTypes = ['crc32', 'md5'];
        if (!in_array($hashType, $availableTypes)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid hash hashType provided '%s'",
                    $hashType
                )
            );
        }

        $this->hashType = $hashType;
    }

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

        // make sure we haven't already add this node
        if (!in_array($node, $this->nodes)) {
            // reset sorted positions, adding a node invalidates
            $this->positions = [];
            // add the node to the nodes array
            $this->nodes[] = $node;
            // calculate how many replicas to use in the circle
            $replicas = round($this->replicas * $weight);
            for ($index = 0; $index < $replicas; $index++) {
                // hashing the node with the index will give us the position in the circle
                $this->circle[$this->hash("{$node}:{$index}")] = $node;
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

        // find the node index for removal
        $nodeIndex = array_search($node, $this->nodes);
        if ($nodeIndex !== false) {
            // reset sorted positions, removing a node invalidates
            $this->positions = [];
            // remove the found node
            unset($this->nodes[$nodeIndex]);
            // loop the positions in the circle
            $positions = array_keys($this->circle);
            foreach ($positions as $position) {
                // if the position on the circle contains the node we're removing
                // then remove it
                if ($this->circle[$position] == $node) {
                    unset($this->circle[$position]);
                }
            }
        }

        return $this;
    }

    /**
     * Hash
     *
     * @param string $value
     * @return string
     */
    protected function hash($value)
    {
        switch ($this->hashType) {
            case 'crc32':
                return crc32($value);

            case 'md5':
                return substr(md5($value), 0, 8);
        }
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

        // this will be our lookup
        $hash = $this->hash($key);
        // if the stored positions are empty then we need to calculate
        // the positions sorted ready for processing
        if (empty($this->positions)) {
            $this->positions = array_keys($this->circle);
            sort($this->positions);
        }

        $collected = [];
        $found = 0;

        // loop though every position
        foreach ($this->positions as $position) {
            // collect positions matching the hash position or above
            if ($position >= $hash) {
                // fetch the node value
                $node = $this->circle[$position];
                // make sure we haven't collected this node already
                if (!in_array($node, $collected)) {
                    // collect the node
                    $collected[] = $node;
                    // increment the found count
                    $found++;
                    // if we've found the amount we need break the loop
                    if ($found == $count) {
                        break;
                    }
                }
            }
        }

        // if the amount we've found is less than the amount we need
        // we have more work to do
        if ($found < $count) {
            // loop though every position
            foreach ($this->positions as $position) {
                // this time we start collecting straight away
                $node = $this->circle[$position];
                // make sure we haven't collected this node already
                if (!in_array($node, $collected)) {
                    // collect the node to return
                    $collected[] = $node;
                    // increment the found count
                    $found++;
                    // if we've found the amount we need break the loop
                    if ($found == $count) {
                        break;
                    }
                }
            }
        }

        // return the nodes we've collected
        return $collected;
    }
}
