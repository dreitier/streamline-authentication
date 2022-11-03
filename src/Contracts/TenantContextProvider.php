<?php

namespace Dreitier\Streamline\Authentication\Contracts;

interface TenantContextProvider
{
    public function getActiveTenant(): mixed;
}
