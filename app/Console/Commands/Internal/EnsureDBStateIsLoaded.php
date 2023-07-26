<?php

namespace App\Console\Commands\Internal;

use Illuminate\Console\Command;

class EnsureDBStateIsLoaded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:load-state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes sure that all essential DB rows exist';

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
        $this->info('Ensuring DB state is present');

        collect([
            new \Database\State\Types\EnsureAssetTypesPresent,
            new \Database\State\Types\EnsureAwardTypesPresent,
            new \Database\State\Types\EnsureBanTypesPresent,
            new \Database\State\Types\EnsureMembershipValuesPresent,
            new \Database\State\Types\EnsureBillingProductsPresent,
            new \Database\State\Types\EnsureForumCategoriesPresent,
            new \Database\State\Types\EnsureForumBoardsPresent,
            new \Database\State\Types\EnsureItemTypesPresent,
            new \Database\State\Types\EnsureReportReasonsPresent,
            new \Database\State\Types\EnsureSetGenresPresent,
            new \Database\State\Types\EnsurePermissionsPresent,
        ])->each->__invoke();

        $this->info('All DB states loaded');

        return Command::SUCCESS;
    }
}
