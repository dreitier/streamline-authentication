<?php

declare(strict_types=1);
namespace Dreitier\Streamline\Authentication\Methods\Enablement;

use Dreitier\Streamline\Authentication\Contracts\IsEnabled;

class BooleanEnabler implements IsEnabled
{
    public function __construct(public readonly bool $enabled)
    {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
