<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Repositories\Contracts;

use Dreitier\Streamline\Authentication\Util\Collection\UserCollection;

interface UserRepository
{
    public function find($key, $value): UserCollection;
}
