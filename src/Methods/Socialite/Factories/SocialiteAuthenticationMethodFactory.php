<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Socialite\Factories;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethodFactory;
use Dreitier\Streamline\Authentication\Contracts\Provider as ProviderContract;
use Dreitier\Streamline\Authentication\Methods\Socialite\Drivers\DefaultDriverAdapter;
use Dreitier\Streamline\Authentication\Methods\SocialiteMethod;

class SocialiteAuthenticationMethodFactory implements AuthenticationMethodFactory
{
    public function create(string $uniqueAuthenticationMethodName, array $configuration = [], ?ProviderContract $provider = null): AuthenticationMethod
    {
        $socialiteDriverName = $configuration['socialite']['driver'] ?? $uniqueAuthenticationMethodName;

        $adapter = null;

        if (isset($configuration['adapter'])) {
            $adapter = (new $configuration['adapter']);
        } else {
            $adapter = new DefaultDriverAdapter($uniqueAuthenticationMethodName);
        }

        $r = new SocialiteMethod($socialiteDriverName, $adapter, $configuration, $provider);

        return $r;
    }
}
