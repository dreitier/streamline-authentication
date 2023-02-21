<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Controllers\DisplayAuthenticationMethodsController;
use Dreitier\Streamline\Authentication\Controllers\LoginController;
use Dreitier\Streamline\Authentication\Facades\SocialiteMethodManager;
use Dreitier\Streamline\Authentication\Methods\FormBased\Controllers\FormBasedController;
use Dreitier\Streamline\Authentication\Methods\MagicLink\Controllers\MagicLinkController;
use Dreitier\Streamline\Authentication\Methods\SelectableUser\Controllers\SelectableUserController;
use Dreitier\Streamline\Authentication\Methods\Socialite\Controllers\SocialiteController;
use Dreitier\Streamline\Authentication\Middlewares\OneTimeSignatureUsage;
use Dreitier\Streamline\Authentication\Providers\Provider;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodSelector;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Psr\Log\LoggerInterface;

class StreamlineAuthenticationMethod
{
    public function __construct(private readonly AuthenticationMethodRepositoryContract $authenticationMethodRepository)
    {

    }

    public static function isEnvironmentActive(...$allowedEnvironments)
    {
        $env = config('app.env');

        if (in_array($env, $allowedEnvironments)) {
            return true;
        }

        return false;
    }
}
