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
        // register routes for socialite
        StreamlineAuthenticationMethod::ifEnabled(SocialiteMethodManager::hasAtLeastOneUsableProvider(), function () {
            Route::get('/flow/{provider}/{id}', [SocialiteController::class, 'handover'])
                ->name(Package::CONFIG_NAMESPACE.'.flow.handover');
            Route::get('/flow/{provider}/{id}/callback', [SocialiteController::class, 'callback'])
                ->name(Package::CONFIG_NAMESPACE.'.flow.callback');
        });

        // login via selectable user for demo purposes
        StreamlineAuthenticationMethod::ifEnabled('selectable_user', function () {
            Route::post('/predefined-email', [SelectableUserController::class, 'process'])
                ->name(Package::key('route.auth.predefined-email'));
        });

        // magic link
        StreamlineAuthenticationMethod::ifEnabled('magic_link_via_email', function () {
            Route::post('/magic-link', [MagicLinkController::class, 'requestMagicLink'])
                ->name(Package::key('route.auth.magic-link.request'));

            Route::middleware([OneTimeSignatureUsage::class])
                ->get('/magic-link/login/{principal}', [MagicLinkController::class, 'login'])
                ->name(Package::key('route.auth.magic-link.login'));
        });

        // email/password
        StreamlineAuthenticationMethod::ifEnabled('form_based', function () {
            Route::post('/form', [FormBasedController::class, 'process'])
                ->name(Package::key('route.auth.form'));
        });
    });
