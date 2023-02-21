<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Facades;

use Illuminate\Support\Facades\Facade;

class StreamlineAuthentication extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Dreitier\Streamline\Authentication\StreamlineAuthentication::class;
    }
}
