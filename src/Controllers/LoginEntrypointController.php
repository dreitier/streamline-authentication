<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Controllers;

use Dreitier\Streamline\Authentication\Controllers\Decorators\LoginPage;
use Dreitier\Streamline\Authentication\Controllers\Decorators\UiAuthenticationMethodDecorator;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodSelector;

class LoginEntrypointController
{
    public function __construct(public readonly AuthenticationMethodRepositoryContract $authenticationMethodRepository)
    {
    }

    public function index()
    {
        // find available authentication methods in the current context
        $authenticationMethods = $this->authenticationMethodRepository->find(AuthenticationMethodSelector::onlyUsables());

        $decorators = [];

        // find all available decorators
        // TODO: This is clumsy, as every authentication method should have decorator
        foreach (Package::config('ui.decorators') as $decoratorClazz) {
            $decorator = new $decoratorClazz;

            if ($decorator instanceof UiAuthenticationMethodDecorator) {
                $decorator->setAuthenticationMethods($authenticationMethods);
            }

            $decorators[] = $decorator;
        }

        $config = new LoginPage(collect($decorators));

        //
        if (false !== ($redirectUrl = $config->triggersDefaultRedirection())) {
            return $redirectUrl;
        }

        return $config->render();
    }
}
