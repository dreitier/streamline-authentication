<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Repositories\Contracts;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Contracts\Provider;

/**
 * Select a set of different authentication methods
 */
class AuthenticationMethodSelector
{
    public function __construct(
        public readonly bool      $includeDisabled = true,
        public readonly bool      $includeUnconfigured = true,
        public readonly ?Provider $provider = null,
        public readonly ?string   $authenticationMethod = null,
    )
    {
    }

    /**
     * Find only authentication methods which are enabled and configured.
     *
     * @return AuthenticationMethodSelector
     */
    public static function onlyUsables()
    {
        return new AuthenticationMethodSelector(includeDisabled: false, includeUnconfigured: false);
    }

    /**
     * Find all authentication methods.
     *
     * @return AuthenticationMethodSelector
     */
    public static function all()
    {
        return new AuthenticationMethodSelector();
    }

    /**
     * Find a single usable authentication method of a given provider configuration.
     *
     * @param Provider $provider
     * @return AuthenticationMethodSelector
     */
    public static function usableProvider(Provider $provider)
    {
        return new AuthenticationMethodSelector(includeDisabled: false, includeUnconfigured: false, provider: $provider);
    }

    /**
     * Find one or multiple methods of a given authentication method class
     *
     * @param string $authenticationMethodClazz
     * @return AuthenticationMethodSelector
     */
    public static function usableMethod(string $authenticationMethodClazz)
    {
        return new AuthenticationMethodSelector(includeDisabled: false, includeUnconfigured: false, authenticationMethod: $authenticationMethodClazz);
    }

    /**
     * Check the given authentication method against this selector.
     *
     * @param AuthenticationMethod $method
     * @return bool
     */
    public function matches(AuthenticationMethod $method)
    {
        if (!$this->includeDisabled && !$method->isEnabled()) {
            return false;
        }

        if ($this->includeUnconfigured && !$method->isConfigured()) {
            return false;
        }

        if ($this->provider && (($this->provider::class != $method->getProvider()::class) || (!$this->provider->matches($method->getProvider())))) {
            return false;
        }

        if ($this->authenticationMethod && ($this->authenticationMethod != $method::class) && (!is_subclass_of($method::class, $this->authenticationMethod))) {
            return false;
        }

        return true;
    }
}
