<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Socialite;

use Exception;

class SocialiteConfigurationException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
