<?php

namespace Tests\Feature\Assets\Uploading;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User\User;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * Test if uploading is working properly
     *
     * @return void
     */
    public function test_uploader_uploads()
    {
        Storage::fake('s3');

        $user = User::factory()->monthOld()->create();

        // should always hash out to fe5664da-a518-5984-9d12-4554b2ce815a
        $file = UploadedFile::fake()->image('test.png', 128, 128);

        $this->actingAs($user)
            ->post($this->addDomain('shop/create/upload', "www"), [
                'title' => 'Test Item',
                'type' => 'tshirt',
                'file' => $file
            ]);

        $this->assertTrue(Storage::disk('s3')->exists('/v3/assets/fe5664da-a518-5984-9d12-4554b2ce815a'));
    }
}
