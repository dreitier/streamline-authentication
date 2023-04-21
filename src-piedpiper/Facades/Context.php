<?php

declare(strict_types=1);

namespace Dreitier\Piedpiper\Facades;

use Dreitier\Piedpiper\Flow\Context\Factory;
use Illuminate\Support\Facades\Facade;

class Context extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }
}
