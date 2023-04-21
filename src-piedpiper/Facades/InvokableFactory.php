<?php

declare(strict_types=1);

namespace Dreitier\Piedpiper\Facades;

use Dreitier\Piedpiper\Step\Invokable\FactoryDelegate;
use Illuminate\Support\Facades\Facade;

class InvokableFactory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FactoryDelegate::class;
    }
}
