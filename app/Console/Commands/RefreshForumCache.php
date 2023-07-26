<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

use App\Models\Forum\ForumBoard;

class RefreshForumCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forum:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh forum cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $boards = ForumBoard::withoutGlobalScope('power')->get();

        foreach ($boards as $board) {
            Cache::put('forum' . $board->id . 'threadCount', $board->threads()->withoutGlobalScope('hidden')->count());
            Cache::put('forum' . $board->id . 'postCount', $board->posts()->count());
        }

        return Command::SUCCESS;
    }
}
