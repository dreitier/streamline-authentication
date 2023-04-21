<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Events;

class Login
{
    public function __construct(public readonly mixed $user)
    {
        throw_if($user == null, "Unable to resolve user");
    }
}
