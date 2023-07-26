<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RequiredSeeder extends Seeder
{
    /**
     * Seed the application's required data
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            Required\PermissionsSeeder::class,
        ]);
    }
}
