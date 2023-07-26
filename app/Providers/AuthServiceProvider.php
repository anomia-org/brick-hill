<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Laravel\Passport\Passport;
use App\Models\Passport\Client as PassportClient;

use App\Models\User\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'App\\Policies\\' . str_replace('App\\Models\\', '', $modelClass) . 'Policy';
        });

        Gate::before(function (User $user, $ability, $arguments) {
            // only admins can have permissions so just ignore the request if they arent an admin
            // give all permissions if they have the super admin role
            // all policies have at least one argument so ignore if it has more than 0
            // then check if the ability being checked for has been defined as a gate already
            // if it hasnt, determine if they are an admin and give them all permissions if they are a super-admin
            if (count($arguments) == 0 && !array_key_exists($ability, Gate::abilities())) {
                return $user->is_admin ? ($user->hasRole('super-admin') ? true : null) : false;
            }
        });

        $this->registerPolicies();

        Passport::useClientModel(PassportClient::class);

        Passport::tokensCan([
            'access-workshop' => 'Access any information relating to the workshop',
        ]);
    }
}
