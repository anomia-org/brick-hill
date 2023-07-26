<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RequiredSeeder::class);

        $this->call([
            User\UserSeeder::class,
            Item\ItemSeeder::class
        ]);

        Artisan::call("scout:import", ["model" => "App\Models\Item\Item"]);
        Artisan::call("scout:import", ["model" => "App\Models\User\User"]);
    }
}
