<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Socialite\Drivers;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationContext;
use Dreitier\Streamline\Authentication\Contracts\ExternalIdentity;

/**
 * Wraps the specialized Socialite driver (e.g. Azure)
 */
interface DriverAdapter
{
    public function getName(): string;

    public function getIconUrl(): ?string;

    public function createIdentity(AuthenticationContext $authenticationContext, $data): ExternalIdentity;
}
