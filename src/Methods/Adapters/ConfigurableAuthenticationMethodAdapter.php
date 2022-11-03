<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Adapters;

use Dreitier\Streamline\Authentication\Contracts\ConfigurableByFactory;
use Dreitier\Streamline\Authentication\Contracts\Provider;

class ConfigurableAuthenticationMethodAdapter extends AuthenticationMethodAdapter implements ConfigurableByFactory
{
    public function configure(array $configuration)
    {
        $this->upsertConfiguration($configuration);
        $this->configured = true;
    }

    public function setProvider(?Provider $provider)
    {
        $this->provider = $provider;
    }
}
