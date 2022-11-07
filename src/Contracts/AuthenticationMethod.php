<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Contracts;

use Dreitier\Streamline\Authentication\Contracts\Provider as ProviderContract;

interface AuthenticationMethod extends IsEnabled
{
    /**
     * Return if the authentication method has been configured to be usable
     *
     * @return bool
     */
    public function isConfigured(): bool;

    /**
     * Return an optional provider of this authentication method instance. You can have multiple providers for a single authentication method.
     *
     * @return Provider|null
     */
    public function getProvider(): ?ProviderContract;
}
