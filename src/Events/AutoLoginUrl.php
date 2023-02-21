<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Events;

use Illuminate\Support\Facades\URL;

class AutoLoginUrl
{
    public function __construct(public readonly mixed $principal,
                                public readonly mixed $user,
                                public readonly int $expirationAfterSeconds,
                                public readonly string $routeName,
                                public readonly array $routeArgs = []
    )
    {
    }

    public function create(): string
    {
        $r = URL::temporarySignedRoute(
            $this->routeName,
            now()->addSecond($this->expirationAfterSeconds),
            $this->routeArgs
        );

        return $r;
    }
}
