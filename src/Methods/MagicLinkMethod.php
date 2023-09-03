<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods;

use Dreitier\Streamline\Authentication\Events\CreateAutoLoginUrls;
use Dreitier\Streamline\Authentication\Facades\AutoLoginUrlFactory;
use Dreitier\Streamline\Authentication\Methods\Adapters\ConfigurableAuthenticationMethodAdapter;
use Dreitier\Streamline\Authentication\Methods\MagicLink\MagicLinkResult;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\UserNotFoundException;

class MagicLinkMethod extends ConfigurableAuthenticationMethodAdapter
{
    public function __construct(private readonly UserRepositoryContract $userRepository)
    {
    }

    public function createMagicLinks($principal, $routeArgs = []): MagicLinkResult
    {
        $users = $this->userRepository->find(Package::configWithDefault('methods.magic_link_via_email.find_by_key'), $principal);

        if ($users->isEmpty()) {
            throw new UserNotFoundException($principal);
        }

        $autoLoginUrls = AutoLoginUrlFactory::create($users);

        return new MagicLinkResult($users, $autoLoginUrls);
    }
}
