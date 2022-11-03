<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Listeners;

use Dreitier\Streamline\Authentication\Events\AuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Events\ExternalAuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Package;

class UpsertUserAfterExternalAuthentication
{
    public function handle(ExternalAuthenticationSucceeded $externalAuthenticationSucceeded)
    {
        $email = $externalAuthenticationSucceeded->externalIdentity->getEmail();
        abort_if(empty($email), 'External authentication must provide an email address');

        $data = $externalAuthenticationSucceeded->externalIdentity->getData();
        $name = $data->name;

        $user = Package::config('user.impl')::updateOrCreate([
            'email' => $email,
        ], [
            'name' => $name,
            'password' => 'random',
        ])->first();

        return event(new AuthenticationSucceeded($user));
    }
}
