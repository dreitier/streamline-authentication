<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Providers\Provider;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodSelector;

class StreamlineAuthenticationMethod
{
    public function __construct(private readonly AuthenticationMethodRepositoryContract $authenticationMethodRepository)
    {

    }

    public function ifEnabled(string|callable $authenticationMethod, ?callable $ifEnabled = null): bool
    {
        $isEnabled = false;

        if (is_string($authenticationMethod)) {
            $selector = AuthenticationMethodSelector::usableProvider(Provider::globalProvider($authenticationMethod));

            if (class_exists($authenticationMethod)) {
                $selector = AuthenticationMethodSelector::usableMethod($authenticationMethod);
            }

            $r = $this->authenticationMethodRepository->find($selector);

            if ($r->count() > 0) {
                $isEnabled = $r->first()->isEnabled();
            }

        } elseif (is_callable($authenticationMethod)) {
            $isEnabled = (bool)$authenticationMethod();

        } else {
            throw_if(true == false, "Provided parameter for ifEnabled is not valid; provide either a unique authentication method name or a callable");
        }

        if ($isEnabled && is_callable($ifEnabled)) {
            $ifEnabled();
        }

        return $isEnabled;
    }


    public static function isEnvironmentActive(...$allowedEnvironments)
    {
        $env = config('app.env');

        if (in_array($env, $allowedEnvironments)) {
            return true;
        }

        return false;
    }
}
