<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Contracts;

interface AuthenticationMethodFactory
{
    /**
     * Create a new authentication method instance.
     *
     * @param  string  $uniqueAuthenticationMethodName
     * @param  array  $configuration
     * @param  Provider|null  $provider
     * @return AuthenticationMethod
     */
    public function create(string $uniqueAuthenticationMethodName, array $configuration, ?Provider $provider): AuthenticationMethod;
}
