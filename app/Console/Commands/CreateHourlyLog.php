<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Models\ActivityLog;
use App\Models\User\User;
use App\Models\Membership\{
    Subscription,
    Membership,
    Payment
};
use App\Models\Forum\{
    ForumThread,
    ForumPost
};

class CreateHourlyLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the hourly log for basic statistics';

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
        if (ActivityLog::where('created_at', '>=', Carbon::now()->subMinutes(30))->exists())
            return Command::FAILURE;
        ActivityLog::create([
            'users' => User::count(),
            'online' => User::where('last_online', '>=', Carbon::now()->subHour())->count(),
            'active_subscriptions' => Subscription::active()->count(),
            'active_memberships' => Membership::active()->count(),
            'posts' => ForumThread::count() + ForumPost::count(),
            'bits' => User::sum('bits') + (User::sum('bucks') * 10),
            'funds_in_cents' => Payment::sum('gross_in_cents')
        ]);

        return Command::SUCCESS;
    }
}
