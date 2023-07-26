<?php

namespace App\Helpers\Assets;

use JsonSerializable;

use App\Helpers\Assets\Internal\Node;

/**
 * Class to create JSON objects to store data compatible with the game client
 * ```php
 * $creator = new \App\Helpers\Assets\Creator;
 * $node = $creator->newNode('shirt', 1234);
 * return $creator;
 * ```
 * @package App\Helpers\Assets
 */
class Creator implements JsonSerializable
{
    /**
     * Stores created nodes to serialize
     * 
     * @var array<Node>
     */
    public array|Node $nodes = [];

    /**
     * Create a new node that is pushed to $this->nodes
     * 
     * @param string $type
     * @return Node 
     */
    public function newNode(string $type)
    {
        $node = new Node($type);
        array_push($this->nodes, $node);

        return $node;
    }

    /**
     * Encodes the class for storage using proper JSON settings
     * 
     * @return string|false 
     */
    public function toJson()
    {
        return json_encode($this, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Define how to serialize the class
     * 
     * @return mixed 
     */
    public function jsonSerialize(): mixed
    {
        return $this->nodes;
    }
}
