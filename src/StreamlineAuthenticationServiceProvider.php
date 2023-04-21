<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethodFactory as AuthenticationMethodFactoryContract;
use Dreitier\Streamline\Authentication\Facades\StreamlineAuthentication;
use Dreitier\Streamline\Authentication\Facades\StreamlineAuthenticationMethod;
use Dreitier\Streamline\Authentication\Methods\Factories\AuthenticationMethodFactory;
use Dreitier\Streamline\Authentication\Methods\Socialite\SocialiteMethodManager;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\UserRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class StreamlineAuthenticationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../assets/config.php', Package::CONFIG_NAMESPACE);

        $this->app->singleton(SocialiteMethodManager::class);
        $this->app->singleton(StreamlineAuthentication::class);
        $this->app->singleton(StreamlineAuthenticationMethod::class);

        $this->app->singleton(AuthenticationMethodFactoryContract::class, function () {
            return new AuthenticationMethodFactory(Package::config('authentication.factories'));
        });

        $this->app->singleton(AuthenticationMethodRepositoryContract::class, Package::config('authentication.repository.impl'));
        $this->app->singleton(UserRepositoryContract::class, function () {
            return new UserRepository();
        });

        $this->loadViewsFrom(__DIR__ . '/../assets/views', Package::CONFIG_NAMESPACE);

        if (config(Package::CONFIG_NAMESPACE . '.routes', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../assets/routes.php');
        }
    }

    public function boot()
    {
        $this->bootPublishers();
        $this->bootEvents();
    }

    protected function bootPublishers()
    {
        $this->publishes([
            __DIR__ . '/../assets/config.php' => config_path(Package::CONFIG_NAMESPACE . '.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../assets/routes.php' => base_path('routes/' . Package::CONFIG_NAMESPACE . '.php'),
        ], 'routes');

        $this->publishes([
            __DIR__ . '/../assets/views' => resource_path('views/vendor/' . Package::CONFIG_NAMESPACE),
        ], 'assets');
    }

    protected function bootEvents()
    {
        foreach ($this->events() as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }

    public function events(): array
    {
        $r = config(Package::CONFIG_NAMESPACE . '.events', []);
        return $r;
    }
}