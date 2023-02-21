<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods;

use Dreitier\Streamline\Authentication\Events\CreateAutoLoginUrls;
use Dreitier\Streamline\Authentication\Methods\Adapters\ConfigurableAuthenticationMethodAdapter;
use Dreitier\Streamline\Authentication\Methods\MagicLink\MagicLinkResult;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;

class MagicLinkMethod extends ConfigurableAuthenticationMethodAdapter
{
    public function __construct(private readonly UserRepositoryContract $userRepository)
    {
    }

    public function createMagicLinks($principal, $routeArgs = []): MagicLinkResult
    {
        $users = $this->userRepository->find(Package::configWithDefault('methods.magic_link_via_email.find_by_key'), $principal);

        if ($users->isEmpty()) {
            throw new \Exception('Unable to find user(s) for principal');
        }

        $createdAutoLoginUrls = get_first_event_response(event(new CreateAutoLoginUrls($users, $routeArgs)));

        return new MagicLinkResult($users, $createdAutoLoginUrls);
    }
}
