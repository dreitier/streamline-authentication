<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\FormBased\Controllers;

use Dreitier\Streamline\Authentication\Controllers\ProvidesAuthenticationMethods;
use Dreitier\Streamline\Authentication\Events\AuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Methods\FormBased\Requests\FormBasedRequest;
use Dreitier\Streamline\Authentication\Methods\FormBasedMethod;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;

/**
 * Accepts a principal (e.g. username, email) and a password.
 */
class FormBasedController
{
    use ProvidesAuthenticationMethods;

    public function __construct(public readonly AuthenticationMethodRepositoryContract $authenticationMethodRepository)
    {
    }

    public function process(FormBasedRequest $request)
    {
        $authenticationMethod = $this->requireAuthenticationMethod(FormBasedMethod::class);
        $response = $authenticationMethod->authenticate($request);

        return get_first_event_response(event(new AuthenticationSucceeded($response->resolvedUser)));
    }
}
