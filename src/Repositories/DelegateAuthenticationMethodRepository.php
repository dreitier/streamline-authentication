<?php

namespace Dreitier\Streamline\Authentication\Repositories;

use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodSelector;
use Dreitier\Streamline\Authentication\Repositories\Contracts\LocateAuthenticationMethodRepository;
use Illuminate\Support\Collection;

class DelegateAuthenticationMethodRepository implements AuthenticationMethodRepositoryContract
{
    private ?AuthenticationMethodRepositoryContract $delegate = null;

    public function __construct(public readonly LocateAuthenticationMethodRepository $strategy)
    {
    }

    private function getDelegate(): AuthenticationMethodRepositoryContract
    {
        if ($this->delegate == null) {
            $this->delegate = $this->strategy->getRepository();
        }

        return $this->delegate;
    }

    public function find(AuthenticationMethodSelector $selector): Collection
    {
        return $this->getDelegate()->find($selector);
    }
}
