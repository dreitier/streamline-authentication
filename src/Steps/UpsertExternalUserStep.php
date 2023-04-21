<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Steps;

use Dreitier\Piedpiper\Flow\Context;
use Dreitier\Piedpiper\Step\Contracts\Step;
use Dreitier\Streamline\Authentication\Events\ExternalAuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Events\Login;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class UpsertExternalUserStep implements Step
{
    public function handle(Context $ctx, \Closure $next): Response|RedirectResponse
    {
        /** @var ExternalAuthenticationSucceeded $externalAuthenticationSucceeded */
        if (!($externalAuthenticationSucceeded = $ctx->get(ExternalAuthenticationSucceeded::class))) {
            $next();
        }

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

        $ctx->push(new Login($user));

        return $next();
    }
}
