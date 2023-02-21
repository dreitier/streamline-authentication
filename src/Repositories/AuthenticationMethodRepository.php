<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Repositories;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethodFactory as AuthenticationMethodFactoryContract;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Providers\Provider;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodSelector;
use Illuminate\Support\Collection;

class AuthenticationMethodRepository implements AuthenticationMethodRepositoryContract
{
    public function __construct(public readonly AuthenticationMethodFactoryContract $methodFactory)
    {
    }

    protected function getAvailableMethods(): array
    {
        return Package::config('methods');
    }

    public function find(AuthenticationMethodSelector $selector): Collection
    {
        $r = [];

        $methods = $this->getAvailableMethods();

        foreach ($methods as $uniqueMethodName => $configuration) {
            if ($configuration instanceof AuthenticationMethod) {
                $method = $configuration;
            } else {
                $method = $this->methodFactory->create($uniqueMethodName, $configuration, Provider::globalProvider($uniqueMethodName));
            }

            if ($selector->matches($method)) {
                $r[] = $method;
            }
        }

        return collect($r);
    }
}
