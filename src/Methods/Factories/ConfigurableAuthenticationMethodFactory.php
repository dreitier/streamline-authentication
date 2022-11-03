<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Factories;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethodFactory as AuthenticationMethodFactoryContract;
use Dreitier\Streamline\Authentication\Contracts\ConfigurableByFactory;
use Dreitier\Streamline\Authentication\Contracts\Provider as ProviderContract;

class ConfigurableAuthenticationMethodFactory implements AuthenticationMethodFactoryContract
{
    public function __construct(public readonly string $clazz)
    {
    }

    public function create(string $uniqueAuthenticationMethodName, array $configuration = [], ?ProviderContract $provider = null): AuthenticationMethod
    {
        $clazz = $this->clazz;
        $instance = app()->make($clazz);

        /** @var ConfigurableByFactory $instance */
        if ($instance instanceof ConfigurableByFactory) {
            $instance->configure($configuration);
            $instance->setProvider($provider);
        }

        return $instance;
    }
}
