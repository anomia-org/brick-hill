<?php

namespace Database\State\Types;

use Database\State\State;

class EnsureAwardTypesPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'award_types';

    /**
     * Array of columns that the $rows array must use
     * 
     * @var array<string>
     */
    protected array $columns = [
        "id", "name", "description"
    ];

    /**
     * Array of rows that should exist
     * 
     * @var array
     */
    protected array $rows = [
        ['id' => 1, 'name' => 'Classic', 'description' => 'Have an account for more than a year'],
        ['id' => 2, 'name' => 'Jackpot', 'description' => 'Congratulations, you have rolled the quadruple aeo!'],
        ['id' => 3, 'name' => 'Admin', 'description' => 'Users with this award work for Brick Hill.'],
        ['id' => 4, 'name' => 'Donator', 'description' => 'Users with this award have donated to the site.'],
        ['id' => 5, 'name' => 'Brick Saint', 'description' => 'Granted to users who particularly stand out to the staff.'],
        ['id' => 6, 'name' => 'Mint', 'description' => 'Users with this award have purchased Mint membership.'],
        ['id' => 7, 'name' => 'Ace', 'description' => 'Users with this award have purchased Ace membership.'],
        ['id' => 8, 'name' => 'Royal', 'description' => 'Users with this award have purchased Royal membership.'],
    ];
}
