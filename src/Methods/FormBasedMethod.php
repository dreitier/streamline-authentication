<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods;

use Dreitier\Streamline\Authentication\Methods\Adapters\ConfigurableAuthenticationMethodAdapter;
use Dreitier\Streamline\Authentication\Methods\FormBased\AuthenticationResult;
use Dreitier\Streamline\Authentication\Methods\FormBased\Requests\FormBasedRequest;

class FormBasedMethod extends ConfigurableAuthenticationMethodAdapter
{
    public function __construct()
    {
    }

    /**
     * Authenticate the user based upon the provided form.
     * As this has been previously validated by Laravel, we just extract the resolved user.
     *
     * @param  FormBasedRequest  $request
     * @return AuthenticationResult
     */
    public function authenticate(FormBasedRequest $request): AuthenticationResult
    {
        return new AuthenticationResult($request->getResolvedUser());
    }
}
