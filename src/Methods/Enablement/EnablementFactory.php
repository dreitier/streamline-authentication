<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Enablement;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Contracts\IsEnabled;

class EnablementFactory
{
    public function __construct(public readonly AuthenticationMethod $context)
    {
    }

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
            if ($dynamicEnabler = $this->createEnablerFromClass($authenticationMethod)) {
                return $dynamicEnabler;
            }

            return new RuleEnabler($authenticationMethod);
        }

        return new BooleanEnabler(false);
    }

    /**
     * Create a new enabler for a class. This can be used to inject the current authentication method.
     *
     * @param string $clazz
     * @return IsEnabled|null
     */
    private function createEnablerFromClass(string $clazz): ?IsEnabled
    {
        if (!class_exists($clazz)) {
            return null;
        }

        $instance = (new $clazz());

        if (!($instance instanceof IsEnabled)) {
            return null;
        }

        if ($instance instanceof AuthenticationMethodAware) {
            $instance->setAuthenticationMethod($this->context);
        }

        return $instance;
    }
}