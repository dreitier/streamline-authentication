<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Repositories\Contracts;

interface LocateAuthenticationMethodRepository
{
    public function getRepository(): AuthenticationMethodRepository;
}
