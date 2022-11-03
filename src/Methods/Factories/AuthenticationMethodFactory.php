<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Factories;

use Dreitier\Streamline\Authentication\ConfigurationException;
use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethodFactory as AuthenticationMethodFactoryContract;
use Dreitier\Streamline\Authentication\Contracts\Provider as ProviderContract;
use Dreitier\Streamline\Authentication\Providers\UnsupportedProviderException;

/**
 * Factory for creating new authentication method instances
 */
class AuthenticationMethodFactory implements AuthenticationMethodFactoryContract
{
    const DEFAULT_FACTORY_KEY = 'default';

    const DEFAULT_IMPL_KEY = 'impl';

    private array $factories = [];

    public function __construct(public readonly array $factoryMap = [])
    {
    }

    /**
     * Get another factory for that authentication method type
     *
     * @param  string  $uniqueAuthenticationMethodName
     * @return AuthenticationMethod
     *
     * @throws UnsupportedProviderException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getDelegatedFactory(string $uniqueAuthenticationMethodName): AuthenticationMethodFactoryContract
    {
        if (! isset($this->factories[$uniqueAuthenticationMethodName])) {
            $r = null;

            if (isset($this->factoryMap[$uniqueAuthenticationMethodName])) {
                $r = app()->make($this->factoryMap[$uniqueAuthenticationMethodName]);
            } else {
                $r = new ConfigurableAuthenticationMethodFactory($uniqueAuthenticationMethodName);
            }

            $this->factories[$uniqueAuthenticationMethodName] = $r;
        }

        $resolvedOrNull = $this->factories[$uniqueAuthenticationMethodName];

        if ($resolvedOrNull !== null) {
            return $resolvedOrNull;
        }

        throw new UnsupportedProviderException($uniqueAuthenticationMethodName, 'Factory for type %s is not supported');
    }

    public function create(string $uniqueAuthenticationMethodName, array $configuration = [], ?ProviderContract $provider = null): AuthenticationMethod
    {
        $default = [
        ];

        if (isset($this->factoryMap[self::DEFAULT_FACTORY_KEY])) {
            $default[self::DEFAULT_IMPL_KEY] = $this->factoryMap[self::DEFAULT_FACTORY_KEY];
        }

        $mergedConfiguration = array_merge($default, $configuration);
        throw_if(empty($mergedConfiguration[self::DEFAULT_IMPL_KEY]), ConfigurationException::class, "No implementation class given for $uniqueAuthenticationMethodName");

        return $this->getDelegatedFactory($mergedConfiguration[self::DEFAULT_IMPL_KEY])->create($uniqueAuthenticationMethodName, $mergedConfiguration, $provider);
    }
}
