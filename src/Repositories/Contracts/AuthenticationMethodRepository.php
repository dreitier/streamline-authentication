<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Repositories\Contracts;

use Illuminate\Support\Collection;

interface AuthenticationMethodRepository
{
    /**
     * Find a given set of authentication methods based upn a selector.
     *
     * @param  AuthenticationMethodSelector  $selector
     * @return Collection
     */
    public function find(AuthenticationMethodSelector $selector): Collection;
}
