<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Contracts;

/**
 * Value objects for an authentication context.
 */
class AuthenticationContext
{
    public function __construct(public readonly AuthenticationMethod $method, public readonly Provider $provider)
    {
    }
}
