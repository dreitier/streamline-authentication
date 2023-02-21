<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication;

use Dreitier\Streamline\Authentication\Controllers\DisplayAuthenticationMethodsController;
use Dreitier\Streamline\Authentication\Controllers\AutoLoginController;
use Dreitier\Streamline\Authentication\Facades\SocialiteMethodManager;
use Dreitier\Streamline\Authentication\Methods\FormBased\Controllers\FormBasedController;
use Dreitier\Streamline\Authentication\Methods\MagicLink\Controllers\MagicLinkController;
use Dreitier\Streamline\Authentication\Methods\SelectableUser\Controllers\SelectableUserController;
use Dreitier\Streamline\Authentication\Methods\Socialite\Controllers\SocialiteController;
use Dreitier\Streamline\Authentication\Middlewares\OneTimeSignatureUsage;
use Dreitier\Streamline\Authentication\Facades\StreamlineAuthenticationMethod;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Psr\Log\LoggerInterface;

class StreamlineAuthentication
{
    public function log(): LoggerInterface
    {
        return Log::channel('stack');
    }

    public function defaultRoutes()
    {
        Route::match(['GET', 'POST'], '/', [DisplayAuthenticationMethodsController::class, 'index'])
            ->name(Package::route('login'));

        // in any case register the magic link controller so that users
        Route::middleware([OneTimeSignatureUsage::class])
            ->get('/login/{principal}', [AutoLoginController::class, 'start'])
            ->name(Package::route('login.start'));

        // register routes for socialite
        Route::get('/flow/{provider}/{id}', [SocialiteController::class, 'handover'])
            ->name(Package::route('flow.handover'));
        Route::get('/flow/{provider}/{id}/callback', [SocialiteController::class, 'callback'])
            ->name(Package::route('flow.callback'));

        // login via selectable user for demo purposes
        Route::post('/predefined-email', [SelectableUserController::class, 'process'])
            ->name(Package::route('auth.predefined-email'));

        // magic link
        Route::post('/magic-link', [MagicLinkController::class, 'requestMagicLink'])
            ->name(Package::route('auth.magic-link.request'));

        // email/password
        Route::post('/form', [FormBasedController::class, 'process'])
            ->name(Package::route('auth.form'));
    }
}
