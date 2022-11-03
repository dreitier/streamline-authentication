<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Socialite\Drivers;

/**
 * Sample driver adapter for Microsoft Azure
 */
class AzureDriverAdapter extends DefaultDriverAdapter
{
    public function __construct()
    {
        parent::__construct('Microsoft Azure');
    }
}
