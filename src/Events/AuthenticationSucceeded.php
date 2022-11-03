<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Events;

class AuthenticationSucceeded
{
    public function __construct(public readonly mixed $user)
    {
    }
}
