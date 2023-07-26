<?php

namespace Database\State\Types;

use Database\State\State;

class EnsureForumBoardsPresent extends State
{
    /**
     * Name of table to check
     * 
     * @var string
     */
    protected string $table = 'forum_boards';

    /**
     * Array of columns that the $rows array must use
     * 
     * @var array<string>
     */
    protected array $columns = [
        "id", "name", "description", "category_id", "power"
    ];

    /**
     * Array of rows that should exist
     * 
     * @var array
     */
    protected array $rows = [
        ['id' => 1, 'name' => 'Brick Hill Hub', 'description' => 'Talk about all things relating to Brick Hill.', 'category_id' => 2, 'power' => 0],
        ['id' => 2, 'name' => 'Off Topic', 'description' => 'This board is for posts unrelated to any other.', 'category_id' => 3, 'power' => 0],
        ['id' => 3, 'name' => 'Marketplace', 'description' => 'Discuss trading, clothes and special items here!', 'category_id' => 2, 'power' => 0],
        ['id' => 4, 'name' => 'Clan Discussion', 'description' => 'Recruit, battle and talk about your clans.', 'category_id' => 2, 'power' => 0],
        ['id' => 5, 'name' => 'Support', 'description' => 'Looking for help? Ask here and someone might help!', 'category_id' => 2, 'power' => 0],
        ['id' => 6, 'name' => 'Conspiracies', 'description' => '', 'category_id' => NULL, 'power' => 0], // no idea what the description is
        ['id' => 7, 'name' => 'Game Talk', 'description' => 'Discuss various video games unrelated to Brick Hill.', 'category_id' => 3, 'power' => 0],
        ['id' => 8, 'name' => 'Suggestions', 'description' => 'Want to make a suggestion? Post here and gather support!', 'category_id' => 2, 'power' => 0],
        ['id' => 9, 'name' => 'Media Discussion', 'description' => 'Here you can discuss music, videos, movies, and much more!', 'category_id' => 3, 'power' => 0],
        ['id' => 10, 'name' => 'Forum Games', 'description' => 'Participate with others in these forum-specific games.', 'category_id' => 3, 'power' => 0],
        ['id' => 11, 'name' => 'Creativity', 'description' => 'Show off your many talents in this board!', 'category_id' => 3, 'power' => 0],
        ['id' => 12, 'name' => 'Creations', 'description' => 'Share or present your finished games to other users.', 'category_id' => 4, 'power' => 0],
        ['id' => 13, 'name' => 'Building', 'description' => 'Looking for help? Have a work in progress project? Post here!', 'category_id' => 4, 'power' => 0],
        ['id' => 14, 'name' => 'Scripting', 'description' => 'If you\'re a beginner in scripting, this board will surely help you out.', 'category_id' => 4, 'power' => 0],
        ['id' => 15, 'name' => 'Admin Forum', 'description' => 'Regular users cannot see this board. Use this board to communicate with other admins.', 'category_id' => 1, 'power' => 5],
    ];
}
