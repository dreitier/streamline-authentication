<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Contracts;

use Dreitier\Streamline\Authentication\Contracts\Provider as ProviderContract;

interface AuthenticationMethod
{
    /**
     * Return if the authentication method is generally enabled.
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Return if the authentication mehtod has been configured to be usable
     *
     * @return bool
     */
    public function isConfigured(): bool;

    /**
     * Return an optional provider of this authentication method instance. You can havce multiple providers for a single authentication method.
     *
     * @return Provider|null
     */
    public function getProvider(): ?ProviderContract;
}
