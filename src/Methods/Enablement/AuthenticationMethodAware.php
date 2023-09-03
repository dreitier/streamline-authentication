<?php

namespace Dreitier\Streamline\Authentication\Methods\Enablement;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;

interface AuthenticationMethodAware
{
    public function setAuthenticationMethod(AuthenticationMethod $context);
}