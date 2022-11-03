<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Providers;

use Exception;

class UnsupportedProviderException extends Exception
{
    public function __construct(string $type, string $message = 'Type %s is not supported')
    {
        parent::__construct(sprintf($message, $type));
    }
}
