<?php

namespace Tests;

use App\Exceptions\Custom\Internal\InvalidDataException;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

use Database\Seeders\RequiredSeeder;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // tests should write their own data, so only seed necessary type info
    // do tests even need some of this data like permissions?
    // might just want to call specific seeders on the tests instead of this being global
    protected string $seeder = RequiredSeeder::class;

    /**
     * Function to easily format URLs without keeping a global URL
     * 
     * @param string $path 
     * @param string $subdomain 
     * @return string 
     */
    protected function addDomain(string $path, string $subdomain = "api"): string
    {
        if (str_starts_with($path, '/'))
            throw new InvalidDataException("TestCase::addDomain path cant start with /");

        $url = match ($subdomain) {
            "admin" => "http://admin.laravel-site.test/",
            "api" => "http://api.laravel-site.test/",
            "www" => "http://laravel-site.test/",
            default => throw new InvalidDataException("Invalid subdomain given to TestCase::addDomain"),
        };

        return $url . $path;
    }
}
