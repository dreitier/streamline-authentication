<?php

namespace Dreitier\Streamline\Authentication\Tenancy;

use Dreitier\Streamline\Authentication\Contracts\TenantContextProvider as TenantContextProviderContract;

class TenantContextProvider implements TenantContextProviderContract
{
    public function getActiveTenant(): mixed
    {
        return null;
    }
}
