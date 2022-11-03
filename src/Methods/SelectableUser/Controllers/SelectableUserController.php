<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\SelectableUser\Controllers;

use Dreitier\Streamline\Authentication\Events\AuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository;

class SelectableUserController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function process()
    {
        $principal = request()->get('principal');
        $user = $this->userRepository->find(Package::configWithDefault('methods.selectable_user.find_by_key', 'email'), $principal)->firstOrFail();

        $responses = event(new AuthenticationSucceeded($user));

        return expect_event_response($responses);
    }
}
