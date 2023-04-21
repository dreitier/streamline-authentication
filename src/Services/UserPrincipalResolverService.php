<?php

namespace Dreitier\Streamline\Authentication\Services;

use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Util\Collection\UserCollection;
use Illuminate\Support\Collection;

class UserPrincipalResolverService
{
    public function resolve(UserCollection $users): Collection
    {
        $principalAttributeOrResolver = Package::configWithDefault('login.user.extract_principal', 'id');

        $defaultPrincipalResolver = function ($user) use ($principalAttributeOrResolver) {
            return $user->{$principalAttributeOrResolver};
        };

        $resolvedPrincipals = [];

        foreach ($users as $user) {
            $resolvedPrincipals[] = is_callable($principalAttributeOrResolver) ? $principalAttributeOrResolver($user) : $defaultPrincipalResolver($user);
        }

        return collect($resolvedPrincipals);
    }
}