<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Events;

use Dreitier\Streamline\Authentication\Util\Collection\UserCollection;

class AuthenticationSucceeded
{
    public function __construct(public readonly UserCollection $users)
    {
    }
}
