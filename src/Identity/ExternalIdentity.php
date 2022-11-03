<?php

namespace Dreitier\Streamline\Authentication\Identity;

use Dreitier\Streamline\Authentication\Contracts\ExternalIdentity as IdentityContract;
use Dreitier\Streamline\Authentication\Contracts\Provider;

/**
 * Value object for an external identity
 */
class ExternalIdentity implements IdentityContract
{
    public function __construct(public readonly Provider $fromOriginProvider,
                                public readonly mixed $externalId,
                                public readonly ?string $email,
                                public readonly mixed $data
    ) {
    }

    public function getExternalId(): mixed
    {
        return $this->externalId;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
