<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class BasicRequestTest extends TestCase
{
    /**
     * Tests a basic request
     *
     * @return void
     */
    public function test_a_basic_request()
    {
        Redis::shouldReceive('get')
            ->once()
            ->with('maintenance')
            ->andReturn(false);

        Redis::shouldReceive('get')
            ->once()
            ->with('site_banner')
            ->andReturn();

        $this->get('http://laravel-site.test/')
            ->assertOk();
    }
}
