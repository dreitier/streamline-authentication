<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Enablement;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Contracts\IsEnabled;

class EnablementFactory
{
    public function create(bool|AuthenticationMethod|callable|string $authenticationMethod): IsEnabled
    {
        if (is_bool($authenticationMethod)) {
            return new BooleanEnabler($authenticationMethod);
        }

        if ($authenticationMethod instanceof AuthenticationMethod) {
            return new BooleanEnabler($authenticationMethod->isEnabled());
        }

        if (is_callable($authenticationMethod)) {
            return new BooleanEnabler($authenticationMethod());
        }

        if (is_string($authenticationMethod)) {
            return new RuleEnabler($authenticationMethod);
        }

        return new BooleanEnabler(false);
    }
}
