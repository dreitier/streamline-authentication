<?php

namespace Dreitier\Streamline\Authentication\Repositories\Strategies;

use Dreitier\Streamline\Authentication\Contracts\TenantContextProvider;
use Dreitier\Streamline\Authentication\Repositories\AuthenticationMethodRepository;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\LocateAuthenticationMethodRepository;

class RepositoryLocatorStrategy implements LocateAuthenticationMethodRepository
{
    public function __construct(public readonly TenantContextProvider $tenantContextProvider)
    {
    }

    public function getRepository(): AuthenticationMethodRepositoryContract
    {
        if ($this->tenantContextProvider->getActiveTenant()) {
            throw new \Exception('No way to handle a multi-tenancy');
        } else {
            return app(AuthenticationMethodRepository::class);
        }
    }
}
