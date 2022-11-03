<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Events;

class UserCreated
{
    public function __construct(public readonly mixed $identity, public readonly mixed $user)
    {
    }
}
