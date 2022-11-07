<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Enablement;

use Dreitier\Streamline\Authentication\Contracts\IsEnabled;
use Dreitier\Streamline\Authentication\StreamlineAuthenticationMethod;

class RuleEnabler implements IsEnabled
{
    private ?bool $cache = null;

    /**
     * Contains a list of helper methods to check if an enabling rule can be used
     * @var array|null
     */
    private ?array $enablingRules = null;

    public function __construct(public readonly string $enablingRulesDefinition)
    {

    }

    public function getEnablingRules(): array
    {
        if ($this->enablingRules == null) {
            $this->enablingRules = [
                'in_environment' => fn(...$args) => StreamlineAuthenticationMethod::isEnvironmentActive(... $args)
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

    public function isEnabled(): bool
    {
        if ($this->cache == null) {
            $this->cache = $this->parseEnablingRules($this->enablingRulesDefinition);
        }

        return $this->cache;
    }
}
