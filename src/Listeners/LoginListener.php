<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Listeners;

use Dreitier\Streamline\Authentication\Events\AuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Events\Login;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Support\Facades\Auth;

class LoginListener
{
    public function handle(Login $loginRequest)
    {
        // setting the guard is important, otherwise authentication will fail after redirection
        Auth::guard(Package::configWithDefault('login.guard', 'web'))->login($loginRequest->user, true);

        $redirectTo = Package::configWithDefault('login.after.redirect_to', '/dashboard');
        return redirect($redirectTo);
    }
}
