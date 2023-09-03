<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\MagicLink\Controllers;

use Dreitier\Streamline\Authentication\Controllers\ProvidesAuthenticationMethods;
use Dreitier\Streamline\Authentication\Events\MagicLinkRequested;
use Dreitier\Streamline\Authentication\Methods\MagicLinkMethod;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\UserNotFoundException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

class MagicLinkController
{
    use ProvidesAuthenticationMethods;

    public function __construct(
        public readonly AuthenticationMethodRepositoryContract $authenticationMethodRepository,
        public readonly UserRepositoryContract                 $userRepository,
    )
    {
    }

    public function requestMagicLink()
    {
        /** @var MagicLinkMethod $authenticationMethod */
        $authenticationMethod = $this->requireAuthenticationMethod(MagicLinkMethod::class);
        $email = request()->get('email');

        $executed = RateLimiter::attempt(
            Session::getId(),
            3,
            function () use ($authenticationMethod, $email) {
                try {
                    $result = $authenticationMethod->createMagicLinks($email, []);
                    MagicLinkRequested::dispatch($email, $result);
                } catch (UserNotFoundException $e) {
                    // swallow
                }
            }
        );

        return redirect()->back()->with('message', 'You have received an email with a login link');
    }
}

