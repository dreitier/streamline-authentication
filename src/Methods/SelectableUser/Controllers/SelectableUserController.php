<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\SelectableUser\Controllers;

use Dreitier\Piedpiper\Pipe;
use Dreitier\Streamline\Authentication\Events\Login;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository;
use Dreitier\Streamline\Authentication\Steps\RedirectToAutoLoginUrlStep;

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

        $pipe = new Pipe([
            RedirectToAutoLoginUrlStep::class
        ]);

        return $pipe->run([
            Login::class => new Login($user->firstOrFail())
        ]);
    }
}
