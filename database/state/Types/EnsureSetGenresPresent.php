<?php

namespace Database\State\Types;

use Database\State\State;

class EnsureSetGenresPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'set_genres';

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
        ['id' => 1, 'name' => 'Adventure'],
        ['id' => 2, 'name' => 'Roleplay'],
        ['id' => 3, 'name' => 'Action'],
        ['id' => 4, 'name' => 'Simulation'],
        ['id' => 5, 'name' => 'Showcase'],
        ['id' => 6, 'name' => 'Minigame'],
        ['id' => 7, 'name' => 'Platformer']
    ];
}
