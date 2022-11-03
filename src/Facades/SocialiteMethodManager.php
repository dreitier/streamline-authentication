<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Facades;

use Illuminate\Support\Facades\Facade;

class SocialiteMethodManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Dreitier\Streamline\Authentication\Methods\Socialite\SocialiteMethodManager::class;
    }
}
