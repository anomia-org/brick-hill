<?php

namespace Database\State\Types;

use Database\State\State;

class EnsurePermissionsPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'permissions';

    /**
     * Array of columns that the $rows array must use
     * 
     * @var array<string>
     */
    protected array $columns = [
        "id", "name", "guard_name"
    ];

    /**
     * Array of rows that should exist
     * 
     * @var array
     */
    // !! this doesnt work in production for some reason? according to the logs its reporting the state as being loaded
    // !! although i do remember in testing this having the behavior of not reporting any failures....
    protected array $rows = [
        ['id' => 1, 'name' => 'manage forum', 'guard_name' => 'web'],
        ['id' => 2, 'name' => 'accept items', 'guard_name' => 'web'],
        ['id' => 3, 'name' => 'accept clans', 'guard_name' => 'web'],
        ['id' => 4, 'name' => 'view reports', 'guard_name' => 'web'],
        ['id' => 5, 'name' => 'manage comments', 'guard_name' => 'web'],
        ['id' => 6, 'name' => 'scrub items', 'guard_name' => 'web'],
        ['id' => 7, 'name' => 'scrub users', 'guard_name' => 'web'],
        ['id' => 8, 'name' => 'scrub sets', 'guard_name' => 'web'],
        ['id' => 9, 'name' => 'scrub clans', 'guard_name' => 'web'],
        ['id' => 10, 'name' => 'ban', 'guard_name' => 'web'],
        ['id' => 11, 'name' => 'grant currency', 'guard_name' => 'web'],
        ['id' => 12, 'name' => 'grant items', 'guard_name' => 'web'],
        ['id' => 13, 'name' => 'view user economy', 'guard_name' => 'web'],
        ['id' => 14, 'name' => 'view linked accounts', 'guard_name' => 'web'],
        ['id' => 15, 'name' => 'view emails', 'guard_name' => 'web'],
        ['id' => 16, 'name' => 'view purchases', 'guard_name' => 'web'],
        ['id' => 17, 'name' => 'manage shop', 'guard_name' => 'web'],
        ['id' => 18, 'name' => 'superban', 'guard_name' => 'web'],
        ['id' => 19, 'name' => 'transfer crate', 'guard_name' => 'web'],
        ['id' => 20, 'name' => 'modify emails', 'guard_name' => 'web'],
    ];
}
