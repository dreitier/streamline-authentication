<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Events;

use Illuminate\Support\Collection;

class ResolvedUserPrincipals
{
    public function __construct(public readonly Collection $principals, public readonly ResolveUserPrincipals $request)
    {
    }

    public function first(): mixed
    {
        return $this->principals->first();
    }
}
