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
		$key = Package::configWithDefault('methods.selectable_user.find_by_key', 'email');
        $user = $this->userRepository->find($key, $principal);

        $responses = event(new AuthenticationSucceeded($user));
        $response = expect_event_response($responses);

        return $response;
    }
}
