<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Facades;

use Dreitier\Streamline\Authentication\Services\AutoLoginUrlFactoryService;
use Illuminate\Support\Facades\Facade;

class AutoLoginUrlFactory extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AutoLoginUrlFactoryService::class;
    }
}
