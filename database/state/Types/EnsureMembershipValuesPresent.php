<?php

namespace Database\State\Types;

use Database\State\State;

class EnsureMembershipValuesPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'membership_values';

    /**
     * Array of columns that the $rows array must use
     * 
     * @var array<string>
     */
    protected array $columns = [
        "id", "name", "daily_bucks", "sets", "items", "create_clans", "join_clans"
    ];

    /**
     * Array of rows that should exist
     * 
     * @var array
     */
    protected array $rows = [
        ['id' => 1, 'name' => 'Free', 'daily_bucks' => 0, 'sets' => 1, 'items' => 0, 'create_clans' => 1, 'join_clans' => 5],
        ['id' => 2, 'name' => 'Mint', 'daily_bucks' => 10, 'sets' => 5, 'items' => 1, 'create_clans' => 5, 'join_clans' => 10],
        ['id' => 3, 'name' => 'Ace', 'daily_bucks' => 20, 'sets' => 10, 'items' => 2, 'create_clans' => 10, 'join_clans' => 20],
        ['id' => 4, 'name' => 'Royal', 'daily_bucks' => 70, 'sets' => 20, 'items' => 3, 'create_clans' => 20, 'join_clans' => 50],
    ];
}
