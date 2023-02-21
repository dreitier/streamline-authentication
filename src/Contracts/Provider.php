<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Contracts;

/**
 * A provider is a single "endpoint" (like configured LDAP server, OAuth client etc.)
 */
interface Provider
{
    /**
     * Name of the provider; this should be equal to the unique authentication method name
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Some unique internal id
     *
     * @return mixed
     */
    public function getId(): mixed;

    public function matches(Provider $other): bool;
}
