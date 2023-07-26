<?php

namespace Database\State\Types;

use Database\State\State;

class EnsureItemTypesPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'item_types';

    /**
     * Array of columns that the $rows array must use
     * 
     * @var array<string>
     */
    protected array $columns = [
        "id", "name"
    ];

    /**
     * Array of rows that should exist
     * 
     * @var array
     */
    protected array $rows = [
        ['id' => 1, 'name' => 'hat'],
        ['id' => 2, 'name' => 'face'],
        ['id' => 3, 'name' => 'tool'],
        ['id' => 4, 'name' => 'head'],
        ['id' => 5, 'name' => 'figure'],
        ['id' => 6, 'name' => 'tshirt'],
        ['id' => 7, 'name' => 'shirt'],
        ['id' => 8, 'name' => 'pants'],
    ];
}
