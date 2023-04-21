<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Socialite\Controllers;

use Dreitier\Piedpiper\Pipe;
use Dreitier\Streamline\Authentication\Events\ExternalAuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Methods\SocialiteMethod;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Providers\Provider;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodSelector;

/**
 * Delegates the authentication to Socialite.
 */
class SocialiteController
{
    public function __construct(public readonly AuthenticationMethodRepositoryContract $authenticationMethodRepository)
    {
    }

    /**
     * Find a Socialite authentication method
     *
     * @param string $provider
     * @param mixed $id
     * @return SocialiteMethod
     */
    private function find(string $provider, mixed $id): SocialiteMethod
    {
        $result = $this->authenticationMethodRepository->find(AuthenticationMethodSelector::usableProvider(Provider::create($provider, $id)));

        $r = $result->firstOrFail();

        abort_if(!($r instanceof SocialiteMethod), 403, 'That authentication method can not be called on this controller');

        return $r;
    }

    /**
     * Create handover to the socialite authentication method with the configured provider.
     *
     * @return mixed
     */
    public function handover()
    {
        $provider = request()->route()->parameter('provider');
        $id = request()->route()->parameter('id');

        return $this->find($provider, $id)->handover();
    }

    /**
     * Receive the callback from the provider configured for that Socialite authentication method.
     * @return mixed
     */
    public function callback()
    {
        $provider = request()->route()->parameter('provider');
        $id = request()->route()->parameter('id');
        $socialiteMethod = $this->find($provider, $id);
        $user = $socialiteMethod->callbackReceived();

        $externalAuthenticationSucceeded = new ExternalAuthenticationSucceeded($socialiteMethod, $user);

        $pipe = new Pipe(Package::config('steps.socialite.pipe', []));

        return $pipe->run([
            ExternalAuthenticationSucceeded::class => $externalAuthenticationSucceeded,
        ]);
    }
}