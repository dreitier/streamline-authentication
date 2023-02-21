<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Controllers;

use Dreitier\Streamline\Authentication\Controllers\ProvidesAuthenticationMethods;
use Dreitier\Streamline\Authentication\Events\AuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Events\Login;
use Dreitier\Streamline\Authentication\Methods\MagicLink\Mailable\LoginWithMagicLinkMailable;
use Dreitier\Streamline\Authentication\Methods\MagicLinkMethod;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AutoLoginController
{
    use ProvidesAuthenticationMethods;

    public function __construct(
        public readonly AuthenticationMethodRepositoryContract $authenticationMethodRepository,
        public readonly UserRepositoryContract                 $userRepository,
    )
    {
    }

    public function start()
    {
        $principal = request()->route()->parameter('principal');
        $resolveBy = Package::configWithDefault('login.user.resolve_by', 'id');

        $defaultUserResolver = function ($principal) use ($resolveBy) {
            return $this->userRepository->find(
                $resolveBy,
                $principal)->first();
        };

        $useResolver = is_callable($resolveBy) ? $resolveBy : $defaultUserResolver;

        abort_if(!is_callable($useResolver), 403, 'User resolver is not callable');

        $user = $useResolver($principal);

        abort_if(!$user, 404, 'User could not be found');

        $r = get_first_event_response(event(new Login($user)));

        return $r;
    }
}
