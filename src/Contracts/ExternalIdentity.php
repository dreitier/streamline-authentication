<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Contracts;

/**
 * Represents an external identity, like a Twitter or Azure user. This abstracts the Socialite user.
 */
interface ExternalIdentity
{
    /**
     * An external unique identity, like a UPN, GUID or Twitter handle
     *
     * @return mixed
     */
    public function getExternalId(): mixed;

    /**
     * An optional email address. Not all providers (like Twitter) allow the retrievement of the email address.
     *
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * Other data coming from the external identity provider.
     *
     * @return mixed
     */
    public function getData(): mixed;
}
