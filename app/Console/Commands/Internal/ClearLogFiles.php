<?php

namespace App\Console\Commands\Internal;

use Illuminate\Console\Command;

class ClearLogFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'internal:clean-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean log files during local development';

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
        file_put_contents(storage_path('logs/info.log'), '');

        return Command::SUCCESS;
    }
}
