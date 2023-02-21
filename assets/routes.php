<?php

declare(strict_types=1);

use Dreitier\Streamline\Authentication\Facades\SocialiteMethodManager;
use Dreitier\Streamline\Authentication\Facades\StreamlineAuthenticationMethod;
use Dreitier\Streamline\Authentication\Methods\FormBased\Controllers\FormBasedController;
use Dreitier\Streamline\Authentication\Methods\MagicLink\Controllers\MagicLinkController;
use Dreitier\Streamline\Authentication\Methods\SelectableUser\Controllers\SelectableUserController;
use Dreitier\Streamline\Authentication\Methods\Socialite\Controllers\SocialiteController;
use Dreitier\Streamline\Authentication\Middlewares\OneTimeSignatureUsage;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Support\Facades\Route;

// put everything in the web middleware so that we have our session. otherwise, the user would not be logged in
Route::middleware(['web'])
    ->prefix('/sign-in')
    ->group(function () {
        StreamlineAuthenticationMethod::defaultRoutes();
    });
