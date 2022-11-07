<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;

class StreamlineAuthenticationMethod
{
    /**
     * Contains a list of helper methods to check if an enabling rule can be used
     * @var array|null
     */
    private ?array $enablingRules = null;

    public function getEnablingRules(): array
    {
        if ($this->enablingRules == null) {
            $this->enablingRules = [
                'in_environment' => fn(...$args) => self::isEnvironmentActive(... $args)
            ];
        }

        return $this->enablingRules;
    }

    /**
     * Parses a list of enabling rules. It is more or less the same format as for Laravel Validation:
     * "rule_1:arg1,arg2|rule_2|rule_3". The first rule that failed. fails the whole rule chain.
     *
     * @param $enablingRulesDefinition
     * @return bool
     */
    public function parseEnablingRules($enablingRulesDefinition)
    {
        $rulesToExecute = explode("|", $enablingRulesDefinition);
        $availableRules = $this->getEnablingRules();

        $r = false;

        foreach ($rulesToExecute as $rule) {
            $splat = explode(":", $rule);

            if (isset($availableRules[$splat[0]])) {
                $args = [];

                if (sizeof($splat) >= 2) {
                    $args = explode(",", $splat[1]);
                }

                $resultOfForwardMethod = $availableRules[$splat[0]](... $args);

                $r = (bool)$resultOfForwardMethod;

                // fail fast
                if (!$r) {
                    return false;
                }
            }
        }

        return $r;
    }

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
            $cfg = Package::config('methods.' . $authenticationMethod, null);

            $isEnabled = !$cfg ? false : Package::configWithDefault('methods.' . $authenticationMethod . '.enabled', true);

            if (is_callable($isEnabled)) {
                $isEnabled = $isEnabled();
            } elseif (is_string($isEnabled)) {
                $isEnabled = $this->parseEnablingRules($isEnabled);
            }
        }

        if ($isEnabled && is_callable($ifEnabled)) {
            $ifEnabled();
        }

        return $isEnabled;
    }

    public function isEnvironmentActive(...$allowedEnvironments)
    {
        if (in_array(config('app.env'), $allowedEnvironments)) {
            return true;
        }

        return false;
    }
}
