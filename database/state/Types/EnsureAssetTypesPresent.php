<?php

namespace Database\State\Types;

use Database\State\State;

class EnsureAssetTypesPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'asset_types';

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
        ['id' => 1, 'name' => 'image'],
        ['id' => 2, 'name' => 'mesh'],
        ['id' => 3, 'name' => 'asset'],
        ['id' => 4, 'name' => 'bundled_brk'],
    ];
}
