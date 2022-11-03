<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Events;

use Dreitier\Streamline\Authentication\Contracts\ExternalIdentity;

class ExternalAuthenticationSucceeded
{
    public function __construct(public readonly ExternalIdentity $externalIdentity)
    {
    }
}
