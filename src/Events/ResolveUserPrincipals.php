<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Events;

use Dreitier\Streamline\Authentication\Util\Collection\UserCollection;

class ResolveUserPrincipals
{
    public function __construct(public readonly UserCollection $users)
    {
    }

    public function first(): mixed
    {
        return $this->users->first();
    }
}
