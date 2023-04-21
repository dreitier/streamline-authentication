<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Facades;

use Dreitier\Streamline\Authentication\Services\UserPrincipalResolverService;
use Illuminate\Support\Facades\Facade;

class UserPrincipalResolver extends Facade
{
    protected static function getFacadeAccessor()
    {
        return UserPrincipalResolverService::class;
    }
}
