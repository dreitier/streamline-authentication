<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods;

use Dreitier\Streamline\Authentication\Methods\Adapters\ConfigurableAuthenticationMethodAdapter;
use Dreitier\Streamline\Authentication\Methods\MagicLink\MagicLinkResult;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;
use Illuminate\Support\Facades\URL;

class MagicLinkMethod extends ConfigurableAuthenticationMethodAdapter
{
    public function __construct(private readonly UserRepositoryContract $userRepository)
    {
    }

    public function expirationAfterSeconds(): int
    {
        $r = $this->configuration['expires_after_seconds'] ?? 5;

        return (int) $r;
    }

    public function createLink($principal, $routeArgs = []): MagicLinkResult
    {
        $user = $this->userRepository->find(Package::configWithDefault('methods.magic_link_via_email.find_by_key'), $principal);

        if (! $user) {
            throw new \Exception('User not found');
        }

        $principalAttributeOrResolver = Package::configWithDefault('methods.magic_link_via_email.route_principal_attribute', 'id');

        $defaultPrincipalResolver = function ($user) use ($principalAttributeOrResolver) {
            return $user->{$principalAttributeOrResolver};
        };

        $principal = is_callable($principalAttributeOrResolver) ? $principalAttributeOrResolver($user) : $defaultPrincipalResolver($user);
        $url = URL::temporarySignedRoute(Package::key('route.auth.magic-link.login'), now()->addSecond($this->expirationAfterSeconds()),
            [
                'principal' => $principal,
                ...$routeArgs,
            ]
        );

        return new MagicLinkResult($user, $url);
    }
}
