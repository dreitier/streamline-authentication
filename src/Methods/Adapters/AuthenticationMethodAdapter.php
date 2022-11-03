<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Adapters;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Contracts\Provider as ProviderContract;

class AuthenticationMethodAdapter implements AuthenticationMethod
{
    protected bool $enabled = true;

    protected bool $configured = false;

    protected ?ProviderContract $provider = null;

    protected array $configuration = [];

    public function __construct(bool $enabled = true,
                                bool $configured = true,
                                ?ProviderContract $provider = null
    ) {
        $this->enabled = $enabled;
        $this->configured = $configured;
        $this->provider = $provider;
    }

    protected function upsertConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        if (isset($configuration['enabled'])) {
            $this->enabled = (bool) $configuration['enabled'];
        }
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    public function getProvider(): ?ProviderContract
    {
        return $this->provider;
    }
}
