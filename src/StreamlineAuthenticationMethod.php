<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;

class StreamlineAuthenticationMethod
{
    public function isEnabled(bool|AuthenticationMethod|callable|string $authenticationMethod, ?callable $ifEnabled = null): bool
    {
        $isEnabled = true;

        if (is_bool($authenticationMethod)) {
            $isEnabled = $authenticationMethod;
        } elseif ($authenticationMethod instanceof AuthenticationMethod) {
            $isEnabled = $authenticationMethod->isEnabled();
        } elseif (is_callable($authenticationMethod)) {
            $isEnabled = $authenticationMethod();
        } else {
            $cfg = Package::config('methods.'.$authenticationMethod, null);

            $isEnabled = ! $cfg ? false : Package::configWithDefault('methods.'.$authenticationMethod.'.enabled', true);

            if (is_callable($isEnabled)) {
                $isEnabled = $isEnabled();
            }
        }

        if ($isEnabled && is_callable($ifEnabled)) {
            $ifEnabled();
        }

        return $isEnabled;
    }

    public function isEnvironmentActive(array $allowedEnvironments = [])
    {
        if (in_array(config('app.env'), $allowedEnvironments)) {
            return true;
        }

        return false;
    }
}
