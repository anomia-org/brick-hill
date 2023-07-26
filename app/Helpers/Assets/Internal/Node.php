<?php

namespace App\Helpers\Assets\Internal;

use App\Exceptions\Custom\Internal\InvalidDataException;
use JsonSerializable;

/**
 * Class to store asset data to serialize with Creator
 * 
 * @package App\Helpers\Assets\Internal
 */
class Node implements JsonSerializable
{
    /**
     * List of types that the game client will be able to properly read
     * 
     * @var array<string>
     */
    private static array $validTypes = [
        'tshirt',
        'shirt',
        'pants',
        'hat',
        'face',
        'tool',
        'head'
    ];

    /**
     * List of keys that the game client will be able to properly read
     * 
     * @var array<string>
     */
    private static array $validKeys = [
        'type',
        'mesh',
        'texture'
    ];

    // TODO: implement a validation function to fully validate the node data to ensure it conforms to standards

    /**
     * Initialize the Node
     * 
     * @param string $type 
     * @return void 
     */
    public function __construct(
        public string $type
    ) {
    }

    /**
     * Add a new value to the node
     * 
     * @param string $key 
     * @param mixed $value 
     * @return Node 
     */
    public function setValue(string $key, mixed $value): Node
    {
        if (!in_array($key, self::$validKeys))
            throw new \App\Exceptions\Custom\Internal\InvalidDataException('Invalid key sent to Node');

        $this->{$key} = $value;

        return $this;
    }

    /**
     * Define how to serialize the class
     * 
     * @return mixed 
     * @throws InvalidDataException 
     */
    public function jsonSerialize(): mixed
    {
        // could serialize on setting but would reqire a setter or magic method
        if (!in_array($this->type, self::$validTypes))
            throw new InvalidDataException('Invalid asset type sent to Node');

        return get_object_vars($this);
    }
}
