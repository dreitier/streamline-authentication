<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Steps;

use Dreitier\Piedpiper\Flow\Context;
use Dreitier\Piedpiper\Step\Contracts\Step;
use Dreitier\Streamline\Authentication\Events\Login;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Support\Facades\Auth;

class LoginStep implements Step
{
    public function handle(Context $ctx, \Closure $next)
    {
        /** @var Login $login */
        if (!($login = $ctx->get(Login::class))) {
            return $next();
        }

        $guardName = Package::configWithDefault('login.guard', 'web');

        // setting the guard is important, otherwise authentication will fail after redirection
        Auth::guard($guardName)->login($login->user, true);

        $redirectTo = Package::configWithDefault('login.after.redirect_to', '/dashboard');

        return response()->redirectTo($redirectTo);
    }
}
