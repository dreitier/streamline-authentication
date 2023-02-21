<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Listeners;

use Dreitier\Streamline\Authentication\Events\AutoLoginUrl;
use Dreitier\Streamline\Authentication\Events\CreateAutoLoginUrls;
use Dreitier\Streamline\Authentication\Events\CreatedAutoLoginUrls;
use Dreitier\Streamline\Authentication\Events\ResolveUserPrincipals;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Support\Facades\URL;

class CreateAutoLoginUrlsListener
{
    public function expirationAfterSeconds(): int
    {
        $r = Package::configWithDefault('login.entrypoint.one_time.expires_after_seconds', 5);

        return (int)$r;
    }

    public function handle(CreateAutoLoginUrls $request)
    {
        $userPrincipalResponse = get_first_event_response(event(new ResolveUserPrincipals($request->users)));
        $applyRouteName = Package::lambdaOrInvokable('login.entrypoint.apply_route_name', fn($defaultRoute, $resolvedUserPrincipal) => $defaultRoute);
        $targetRouteName = $applyRouteName(Package::route('login.start'), $userPrincipalResponse);

        $autoLoginUrls = $userPrincipalResponse->principals->map(function ($principal, $index) use ($request, $userPrincipalResponse, $targetRouteName) {
            $user = $userPrincipalResponse->request->users->get($index);
            $defaultRouteArgs = [
                'principal' => $principal,
                ...$request->routeArgs,
            ];

            $applyRouteArgs = Package::lambdaOrInvokable('login.entrypoint.apply_route_args', fn($routeArgs, $userPrincipalResponse, $routeName, $user) => $routeArgs);
            $targetRouteArgs = $applyRouteArgs($defaultRouteArgs, $userPrincipalResponse, $targetRouteName, $user);

            return new AutoLoginUrl(
                principal: $principal,
                user: $user,
                expirationAfterSeconds: $this->expirationAfterSeconds(),
                routeName: $targetRouteName,
                routeArgs: $targetRouteArgs
            );
        });

        return new CreatedAutoLoginUrls(
            autoLoginUrls: $autoLoginUrls,
            request: $request
        );
    }
}
