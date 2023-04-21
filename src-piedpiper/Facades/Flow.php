<?php

declare(strict_types=1);

namespace Dreitier\Piedpiper\Facades;

use Dreitier\Piedpiper\Flow\Factory;
use Illuminate\Support\Facades\Facade;

class Flow extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
