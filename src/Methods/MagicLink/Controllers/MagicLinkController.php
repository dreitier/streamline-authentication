<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\MagicLink\Controllers;

use Dreitier\Streamline\Authentication\Controllers\ProvidesAuthenticationMethods;
use Dreitier\Streamline\Authentication\Events\MagicLinkRequested;
use Dreitier\Streamline\Authentication\Methods\MagicLink\MagicLinkResult;
use Dreitier\Streamline\Authentication\Methods\MagicLink\Mailable\LoginWithMagicLinkMailable;
use Dreitier\Streamline\Authentication\Methods\MagicLinkMethod;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Facades\Mail;

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

        $result = $authenticationMethod->createMagicLinks($email, []);
        MagicLinkRequested::dispatch($email, $result);

        return redirect()->back()->with('message', 'You have received an email with a login link');
    }
}

