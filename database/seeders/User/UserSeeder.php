<?php

namespace Database\Seeders\User;

use App\Constants\Thumbnails\ThumbnailType;
use Illuminate\Database\Seeder;

use App\Models\User\{
    User,
    PlayedSet
};
use App\Models\Forum\{
    ForumPost,
    ForumThread
};
use App\Models\Item\Item;
use App\Models\Polymorphic\Thumbnail;
use App\Models\Set\Set;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create lots of other users
        User::factory()
            ->count(50)
            ->monthOld()
            ->hasEmail()
            ->has(
                ForumThread::factory()
                    ->has(
                        ForumPost::factory()
                            ->state(function (array $attributes, ForumThread $thread) {
                                return ['author_id' => $thread->author_id];
                            }),
                        'posts'
                    ),
                'userThreads'
            )
            ->state(function () {
                return [
                    'is_verified_designer' => 1,
                ];
            })
            ->has(Set::factory())
            ->create();

        // create one user with guaranteed username to login to
        User::factory()
            ->hasEmail()
            ->admin()
            ->monthOld()
            ->rich()
            ->has(Set::factory()->count(10))
            ->has(PlayedSet::factory()->count(40))
            ->has(
                Item::factory()
                    ->hasProduct()
                    ->state(function (array $attributes) {
                        return ['type_id' => 6];
                    })
                    ->count(5)
            )
            ->create([
                'username' => 'Test Name'
            ])
            ->assignRole('super-admin');

        $thumbnail = Thumbnail::factory()->create();

        User::all()->each(
            function (User $user) use ($thumbnail) {
                if ($user->id != 10) {
                    $user->thumbnails()->attach($thumbnail->id, ['thumbnail_type' => ThumbnailType::AVATAR_FULL]);
                }
            }
        );
    }
}
