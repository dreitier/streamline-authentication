<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Contracts;

interface IsEnabled
{
    /**
     * Return if a given authentication method is enabled
     * @return bool
     */
    public function isEnabled(): bool;
}
