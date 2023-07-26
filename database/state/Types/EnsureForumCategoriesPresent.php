<?php

namespace Database\State\Types;

use Database\State\State;

class EnsureForumCategoriesPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'forum_categories';

    /**
     * Array of columns that the $rows array must use
     * 
     * @var array<string>
     */
    protected array $columns = [
        "id", "order", "title", "color"
    ];

    /**
     * Array of rows that should exist
     * 
     * @var array
     */
    protected array $rows = [
        ['id' => 1, 'order' => 1, 'title' => 'Admin', 'color' => 'red'],
        ['id' => 2, 'order' => 2, 'title' => 'Brick Hill', 'color' => 'blue'],
        ['id' => 3, 'order' => 3, 'title' => 'Random', 'color' => 'green'],
        ['id' => 4, 'order' => 4, 'title' => 'Workshop', 'color' => 'orange'],
    ];
}
