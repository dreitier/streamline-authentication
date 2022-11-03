<?php

namespace Dreitier\Streamline\Authentication\Controllers;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodSelector;

/**
 * Include this trait to ensure that the authentication method can only be used if it is usable
 */
trait ProvidesAuthenticationMethods
{
    public function requireAuthenticationMethod($authenticationMethodClazz): AuthenticationMethod
    {
        $result = $this->authenticationMethodRepository->find(AuthenticationMethodSelector::usableMethod($authenticationMethodClazz));

        $r = $result->firstOrFail();

        abort_if(! ($r instanceof $authenticationMethodClazz), 403, 'That authentication method can not be called on this controller');

        return $r;
    }
}
