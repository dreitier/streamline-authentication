<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Listeners;

use Dreitier\Streamline\Authentication\Events\AuthenticationSucceeded;
use Illuminate\Support\Facades\Auth;

class LoginAfterUserExists
{
    public function handle(AuthenticationSucceeded $authenticationSucceeded)
    {
        Auth::guard()->login($authenticationSucceeded->user);

        return redirect('/dashboard');
    }
}
