<?php

namespace Dreitier\Streamline\Authentication\Controllers\Decorators;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Illuminate\Support\Collection;

trait AcceptsAuthenticationMethods
{
    protected Collection $authenticationMethods;

    /**
     * Set a collection of authentication methods to this instance.
     *
     * @param  Collection  $authenticationMethods
     * @return void
     */
    public function setAuthenticationMethods(Collection $authenticationMethods)
    {
        $this->authenticationMethods = $authenticationMethods;
    }

    /**
     * Find all authentication methods of a given type
     *
     * @param $type
     * @return Collection
     */
    public function allAuthenticationMethods($type): Collection
    {
        return $this->authenticationMethods->where(fn ($item) => $item instanceof $type);
    }

    /**
     * Find the first instance which is of a given type
     *
     * @param $type
     * @return AuthenticationMethod|null
     */
    public function firstAuthenticationMethod($type): null|AuthenticationMethod
    {
        return $this->allAuthenticationMethods($type)->first();
    }

    /**
     * Return if the authentication methods in this trait have at least one of that type.
     *
     * @param $type
     * @return bool
     */
    public function hasAuthenticationMethod($type): bool
    {
        return $this->firstAuthenticationMethod($type) !== null;
    }
}
