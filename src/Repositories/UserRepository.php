<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Repositories;

use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository as UserRepositoryContract;
use Dreitier\Streamline\Authentication\Util\Collection\UserCollection;

class UserRepository implements UserRepositoryContract
{
    public function __construct()
    {
    }

    public function find($key, $value): UserCollection
    {
        $result = Package::userQueryBuilder()->where($key, $value)->get()->all();

        return UserCollection::of($result);
    }
}
