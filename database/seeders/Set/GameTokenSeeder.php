<?php

namespace Database\Seeders\Set;

use Illuminate\Database\Seeder;

use App\Models\Set\{
    Set,
    GameToken
};
use App\Models\User\User;

class GameTokenSeeder extends Seeder
{
    /**
     * Used to load test the PostServer API
     * JS used:
     * let y = []; for(let i = 1; i <= 500; i++) { y.push(i); }
     * axios.post(`http://api.laravel-site.test/v1/games/postServer`, {host_key: "lTtGvZvNC2BBdcxVpuENYBDvPjuN5N5JQi4GbnWA2Ik8SWbjux3r6gCaX9wre0bB", port: "25565", players: y})
     */

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->has(
                Set::factory()
                    ->state(['host_key' => 'lTtGvZvNC2BBdcxVpuENYBDvPjuN5N5JQi4GbnWA2Ik8SWbjux3r6gCaX9wre0bB'])
                    ->has(GameToken::factory()->count(500))
            )
            ->create();
    }
}
