<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\MagicLink\Controllers;

use Dreitier\Streamline\Authentication\Controllers\ProvidesAuthenticationMethods;
use Dreitier\Streamline\Authentication\Events\AuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Methods\MagicLink\Mailable\LoginWithMagicLinkMailable;
use Dreitier\Streamline\Authentication\Methods\MagicLinkMethod;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;
use Illuminate\Support\Facades\Mail;

class MagicLinkController
{
    use ProvidesAuthenticationMethods;

    public function __construct(
        public readonly AuthenticationMethodRepositoryContract $authenticationMethodRepository,
        public readonly UserRepositoryContract $userRepository,
    ) {
    }

    public function requestMagicLink()
    {
        /** @var MagicLinkMethod $authenticationMethod */
        $authenticationMethod = $this->requireAuthenticationMethod(MagicLinkMethod::class);
        $email = request()->get('email');

        try {
            $result = $authenticationMethod->createLink($email, []);
            Mail::to($email)->send(new LoginWithMagicLinkMailable($result->user, $result->magicLink));
        } catch (\Exception $e) {
            // swallow
        }

        return redirect()->back()->with('message', 'You have received an email with a login link');
    }

    public function login($principal)
    {
        $userResolver = function ($principal) {
            return $this->userRepository->find(
                Package::configWithDefault('methods.magic_link_via_email.route_principal_attribute', 'id'),
                $principal);
        };

        $customResolver = Package::configWithDefault('methods.methods.magic_link_via_email.resolve_user_from_route_principal');
        $useResolver = $customResolver ?? $userResolver;

        abort_if(! is_callable($useResolver), 403, 'User resolver is not callable');

        $user = $useResolver($principal);

        abort_if(! $user, 404, 'User could not be found');

        return get_first_event_response(event(new AuthenticationSucceeded($user)));
    }
}
