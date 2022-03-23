<?php

namespace App\Providers;

use App\Repositories\UserRepository;
use App\Services\ApplicationService;
use App\Services\HaBook\TeamModel\TeamModelBearerTokenValidator;
use App\Services\HaBook\TeamModel\TeamModelTokenGuard;
use Illuminate\Auth\RequestGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\ResourceServer;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(now()->addDays(15));

        Passport::refreshTokensExpireIn(now()->addDays(30));

        Passport::personalAccessTokensExpireIn(now()->addMonths(1));

        // 新增 Team Model Token Guard，專門驗證 Team Model Server 發出的 Token
        Auth::extend('team_model_token', function ($app, $name, array $config) {
            return new RequestGuard(function ($request) use ($config) {
                return (new TeamModelTokenGuard(
                    $this->makeResourceServer(),
                    Auth::createUserProvider($config['provider']),
                    $this->app->make(UserRepository::class)
                ))->user($request);
            }, $this->app['request']);
        });
    }

    /**
     * Create a ResourceServer instance
     * @return ResourceServer
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function makeResourceServer(): ResourceServer
    {
        return new ResourceServer(
            $this->app->make(AccessTokenRepository::class),
            $this->makeCryptKey('oauth-public.key'),
            new TeamModelBearerTokenValidator()
        );
    }

    /**
     * Create a CryptKey instance without permissions check
     *
     * @param string $key
     * @return \League\OAuth2\Server\CryptKey
     */
    protected function makeCryptKey(string $key): CryptKey
    {
        return new CryptKey(
            'file://' . Passport::keyPath($key),
            null,
            false
        );
    }
}
