<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Controllers;

use Dreitier\Streamline\Authentication\Events\Login;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;
use Dreitier\Streamline\Authentication\Steps\LoginStep;
use Dreitier\Piedpiper\Pipe;

class AutoLoginController
{
    use ProvidesAuthenticationMethods;

    public function __construct(
        public readonly AuthenticationMethodRepositoryContract $authenticationMethodRepository,
        public readonly UserRepositoryContract                 $userRepository,
    )
    {
    }

    public function start()
    {
        $principal = request()->route()->parameter('principal');
        $resolveBy = Package::configWithDefault('login.user.resolve_by', 'id');

        $defaultUserResolver = function ($principal) use ($resolveBy) {
            return $this->userRepository->find(
                $resolveBy,
                $principal)->first();
        };

        $useResolver = is_callable($resolveBy) ? $resolveBy : $defaultUserResolver;

        abort_if(!is_callable($useResolver), 403, 'User resolver is not callable');

        $user = $useResolver($principal);

        abort_if(!$user, 404, 'User could not be found');

        $pipe = new Pipe([
            LoginStep::class,
        ]);

        return $pipe->run([
            Login::class => new Login($user)
        ]);
    }
}
