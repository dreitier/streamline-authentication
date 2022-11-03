<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Listeners;

use Dreitier\Streamline\Authentication\Events\AuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Events\ExternalAuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Package;

class RequireExistingAccountAfterAuthentication
{
    public function handle(ExternalAuthenticationSucceeded $externalAuthenticationSucceeded)
    {
        $email = $externalAuthenticationSucceeded->externalIdentity->getEmail();
        abort_if(empty($email), 'External authentication must provide an email address');

        $user = Package::config('user.impl')::where('email', $email)->first();
        abort_if(! $user, 403, 'The given user does not exist in our database');

        return event(new AuthenticationSucceeded($user));
    }
}
