<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Blade;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Laravel\Passport\Passport;
use Laravel\Scout\EngineManager;
use App\Extensions\Search\OpenSearchEngine;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Passport::hashClientSecrets();

        //Model::preventLazyLoading(!app()->isProduction());

        // should this be an env variable?
        // just enable if on testing?
        if (config('app.fake_notifications')) {
            //Notification::fake();
        }

        if (config('site.logs.db_queries'))
            \DB::listen(function ($query) {
                \Log::info($query->sql, $query->bindings);
            });

        Relation::enforceMorphMap([
            1 => 'App\Models\Item\Item',
            2 => 'App\Models\User\User',
            3 => 'App\Models\Set\Set',
            4 => 'App\Models\Forum\ForumThread',
            5 => 'App\Models\Forum\ForumPost',
            6 => 'App\Models\Polymorphic\Comment',
            7 => 'App\Models\User\Message',
            8 => 'App\Models\Clan\Clan',
            9 => 'App\Models\Set\SetPass',
            10 => 'App\Models\Permission\Permission',
            11 => 'App\Models\User\Outfit',
            12 => 'App\Models\Item\Version',
        ]);

        Blade::directive('noAds', function () {
            // someone find a better way to do this
            // nobody has found a better way yet :(
            return "<?php config(['site.no_ads' => true]) ?>";
        });
        Blade::if('ads', function () {
            return (!auth()->check() || !(auth()->user()->membership ?? false)) &&
                (app()->environment(['prod', 'production'])) &&
                (!config('site.no_ads'));
        });

        Blade::directive('noBanner', function () {
            return "<?php config(['site.no_banner' => true]) ?>";
        });
        Blade::if('banner', function () {
            return !config('site.no_banner');
        });

        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.simple-default');

        resolve(EngineManager::class)->extend('opensearch', function () {
            $client = (new \OpenSearch\ClientBuilder())
                ->setHosts(config('scout.opensearch.hosts'))
                ->setSSLVerification(config('scout.opensearch.ssl'));

            if (App::environment(['local', 'testing'])) {
                $client->setBasicAuthentication(config('scout.opensearch.username'), config('scout.opensearch.password'));
            } else {
                $client->setSigV4Region('us-east-1')
                    ->setSigV4CredentialProvider(true);
            }

            $client = $client->build();

            return new OpenSearchEngine($client, config('scout.soft_delete'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();
    }
}
