<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Services;

use Dreitier\Streamline\Authentication\Events\AutoLoginUrl;
use Dreitier\Streamline\Authentication\Facades\UserPrincipalResolver;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Util\Collection\UserCollection;

class AutoLoginUrlFactoryService
{
    public function expirationAfterSeconds(): int
    {
        $r = Package::configWithDefault('login.entrypoint.one_time.expires_after_seconds', 5);

        return (int)$r;
    }

    public function create(UserCollection $users, array $routeArgs = [], ?int $overwriteExpiration = null): array
    {
        $resolvedUserPrincipals = UserPrincipalResolver::resolve($users);

        $applyRouteName = Package::lambdaOrInvokable('login.entrypoint.apply_route_name', fn($defaultRoute, $resolvedUserPrincipals) => $defaultRoute);
        $targetRouteName = $applyRouteName(Package::route('login.start'), $resolvedUserPrincipals);

        $autoLoginUrls = $resolvedUserPrincipals->map(function ($principal, $index) use ($routeArgs, $users, $targetRouteName) {
            $user = $users->get($index);
            $defaultRouteArgs = [
                'principal' => $principal,
                ...$routeArgs,
            ];

            $applyRouteArgs = Package::lambdaOrInvokable('login.entrypoint.apply_route_args', fn($routeName, $routeArgs, $principal, $user) => $routeArgs);
            $targetRouteArgs = $applyRouteArgs($targetRouteName, $defaultRouteArgs, $principal, $user);

            return new AutoLoginUrl(
                principal: $principal,
                user: $user,
                expirationAfterSeconds: $overwriteExpiration ?? $this->expirationAfterSeconds(),
                routeName: $targetRouteName,
                routeArgs: $targetRouteArgs
            );
        });

        return $autoLoginUrls->toArray();
    }
}
