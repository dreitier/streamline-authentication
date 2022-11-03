<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Repositories;

use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;

class UserRepository implements UserRepositoryContract
{
    public function __construct()
    {
    }

    public function find($key, $value): null|object
    {
        $r = Package::userModel()::where($key, $value)->first();

        return $r;
    }
}
