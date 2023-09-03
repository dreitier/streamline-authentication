<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Adapters;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Contracts\IsEnabled;
use Dreitier\Streamline\Authentication\Contracts\Provider as ProviderContract;
use Dreitier\Streamline\Authentication\Methods\Enablement\BooleanEnabler;
use Dreitier\Streamline\Authentication\Methods\Enablement\EnablementFactory;

class AuthenticationMethodAdapter implements AuthenticationMethod
{
    protected ?IsEnabled $isEnabledDelegate = null;

    protected bool $configured = false;

    protected ?ProviderContract $provider = null;

    protected array $configuration = [];

    public function __construct(bool              $enabled = true,
                                bool              $configured = true,
                                ?ProviderContract $provider = null
    )
    {
        $this->isEnabledDelegate = new BooleanEnabler($enabled);
        $this->configured = $configured;
        $this->provider = $provider;
    }

    protected function upsertConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        if (isset($configuration['enabled'])) {
            $this->isEnabledDelegate = (new EnablementFactory($this))->create($configuration['enabled']);
        }
    }

    public function isEnabled(): bool
    {
        if (!$this->isEnabledDelegate) {
            $this->isEnabledDelegate = new BooleanEnabler(false);
        }

        return $this->isEnabledDelegate->isEnabled();
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
