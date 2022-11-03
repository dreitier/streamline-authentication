<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Socialite\Drivers;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationContext;
use Dreitier\Streamline\Authentication\Identity\ExternalIdentity;

class DefaultDriverAdapter implements DriverAdapter
{
    public function __construct(public readonly string $name)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIconUrl(): ?string
    {
        return null;
    }

    /**
     * @param  \Laravel\Socialite\Contracts\User  $data
     * @return ExternalIdentity
     */
    public function createIdentity(AuthenticationContext $authenticationContext, $data): \Dreitier\Streamline\Authentication\Contracts\ExternalIdentity
    {
        return new ExternalIdentity($authenticationContext->provider,
            $data->getId(),
            $data->getEmail(),
            $data
        );
    }

    public function configureAdditionalConnectionParameter($args = [])
    {
        return $args;
    }
}
