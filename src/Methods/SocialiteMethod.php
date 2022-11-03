<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods;

use Dreitier\Streamline\Authentication\ConfigurationException;
use Dreitier\Streamline\Authentication\Contracts\AuthenticationContext;
use Dreitier\Streamline\Authentication\Contracts\ExternalIdentity as IdentityContract;
use Dreitier\Streamline\Authentication\Contracts\Provider;
use Dreitier\Streamline\Authentication\Methods\Adapters\AuthenticationMethodAdapter;
use Dreitier\Streamline\Authentication\Methods\Socialite\Drivers\DriverAdapter;
use Dreitier\Streamline\Authentication\Methods\Socialite\SocialiteConfigurationException;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Contracts\Container\BindingResolutionException;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\Config;

class SocialiteMethod extends AuthenticationMethodAdapter
{
    private array $socialiteConnectionConfiguration = [];

    private string $socialiteDriverName = '';

    private $socialiteDriver = null;

    private DriverAdapter $driverAdapter;

    public function __construct(string $socialiteDriverName, DriverAdapter $driverAdapter, array $configuration = [], ?Provider $provider = null)
    {
        throw_if(empty($socialiteDriverName), SocialiteConfigurationException::class, 'Socialite driver must be configured');
        $this->socialiteDriverName = $socialiteDriverName;
        $this->driverAdapter = $driverAdapter;
        $this->provider = $provider;

        $this->upsertConfiguration($configuration);
        $this->socialiteConnectionConfiguration = $configuration['socialite'] ?? [];

        if (! empty($this->socialiteConnectionConfiguration)) {
            $this->configured = true;
        }
    }

    public function getDriverAdapter(): DriverAdapter
    {
        return $this->driverAdapter;
    }

    private function toRouteArgs()
    {
        throw_if(! $this->provider, SocialiteConfigurationException::class, 'Cannot create route arguments, provider has not been set');

        return ['provider' => $this->provider->getType(), 'id' => $this->provider->getId()];
    }

    public function toHandoverUrl()
    {
        return route(Package::CONFIG_NAMESPACE.'.flow.handover', $this->toRouteArgs());
    }

    public function toCallbackUri()
    {
        return route(Package::CONFIG_NAMESPACE.'.flow.callback', $this->toRouteArgs());
    }

    public function driver()
    {
        if ($this->socialiteDriver != null) {
            return $this->socialiteDriver;
        }

        throw_if(! isset($this->socialiteConnectionConfiguration['client_id']), ConfigurationException::class, 'Key client_id is missing for provider '.$this->provider);
        throw_if(! isset($this->socialiteConnectionConfiguration['client_secret']), ConfigurationException::class, 'Key client_secret is missing for provider '.$this->provider);

        $args = $this->driverAdapter->configureAdditionalConnectionParameter($this->socialiteConnectionConfiguration);

        $callbackUri = $this->toCallbackUri();

        // This is a bug (?) inside Socialite. e.g. services.azure.redirect has to be set, even if it is provided as parameter for Config(...)
        config(['services.'.$this->socialiteDriverName.'.redirect' => $callbackUri]);
        config(['services.'.$this->socialiteDriverName.'.client_id' => $this->socialiteConnectionConfiguration['client_id']]);
        config(['services.'.$this->socialiteDriverName.'.client_secret' => $this->socialiteConnectionConfiguration['client_secret']]);

        $config = new Config(
            $this->socialiteConnectionConfiguration['client_id'],
            $this->socialiteConnectionConfiguration['client_secret'],
            $callbackUri,
            $args,
        );

        //   event(new RequireSocialiteDriverReconfiguration($this));
        try {
            $this->socialiteDriver = Socialite::driver($this->socialiteDriverName)
                ->setConfig($config)
                ->stateless();
        } catch (\Exception $e) {
            // add \SocialiteProviders\Manager\ServiceProvider::class to ServiceProvider
            rethrow_if($e, BindingResolutionException::class, SocialiteConfigurationException::class, 'Socialite is not active. Do you have added Socialite to your service providers?');
            // maybe driver is missing
            throw_if(true, SocialiteConfigurationException::class, 'Unable to create Socialite driver for provider '.$this->provider.': '.$e->getMessage());
        }

        return $this->socialiteDriver;
    }

    public function handover(): mixed
    {
        return $this->driver()->redirect();
    }

    public function callbackReceived(): IdentityContract
    {
        $userObject = $this->driver()->user();

        return $this->getDriverAdapter()->createIdentity(new AuthenticationContext($this, $this->provider), $userObject);
    }
}
