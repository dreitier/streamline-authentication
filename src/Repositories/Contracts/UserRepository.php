<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Repositories\Contracts;

interface UserRepository
{
    public function find($key, $value): null|object;
}
