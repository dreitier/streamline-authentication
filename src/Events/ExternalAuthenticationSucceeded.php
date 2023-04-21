<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Events;

use Dreitier\Streamline\Authentication\Contracts\ExternalIdentity;
use Dreitier\Streamline\Authentication\Methods\SocialiteMethod;

class ExternalAuthenticationSucceeded
{
    public function __construct(public readonly SocialiteMethod $socialiteMethod,
                                public readonly ExternalIdentity $externalIdentity,
    )
    {
    }
}
