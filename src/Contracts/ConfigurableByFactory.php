<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Contracts;

/**
 * Configures an authentication method by setting additional parameters.
 */
interface ConfigurableByFactory
{
    public function configure(array $configuration);

    /**
     * Set an optional provider
     *
     * @param  Provider|null  $provider
     * @return mixed
     */
    public function setProvider(?Provider $provider);
}
