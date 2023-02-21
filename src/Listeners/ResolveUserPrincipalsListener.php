<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Listeners;

use Dreitier\Streamline\Authentication\Events\AuthenticatedSessionUrl;
use Dreitier\Streamline\Authentication\Events\AuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Events\CreatedAutoLoginUrls;
use Dreitier\Streamline\Authentication\Events\ResolveUserPrincipal;
use Dreitier\Streamline\Authentication\Events\ResolvedUserPrincipals;
use Dreitier\Streamline\Authentication\Events\Login;
use Dreitier\Streamline\Authentication\Events\ResolveUserPrincipals;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class ResolveUserPrincipalsListener
{
    public function handle(ResolveUserPrincipals $request): ResolvedUserPrincipals
    {
        $principalAttributeOrResolver = Package::configWithDefault('login.user.extract_principal', 'id');

        $defaultPrincipalResolver = function ($user) use ($principalAttributeOrResolver) {
            return $user->{$principalAttributeOrResolver};
        };

        $resolvedPrincipals = [];

        foreach ($request->users as $user) {
            $resolvedPrincipals[] = is_callable($principalAttributeOrResolver) ? $principalAttributeOrResolver($user) : $defaultPrincipalResolver($user);
        }

        return new ResolvedUserPrincipals(principals: collect($resolvedPrincipals), request: $request);
    }
}
