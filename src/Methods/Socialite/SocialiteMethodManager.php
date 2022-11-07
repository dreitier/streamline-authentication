<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Methods\Socialite;

use Dreitier\Streamline\Authentication\Methods\SocialiteMethod;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodRepository as AuthenticationMethodRepositoryContract;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodSelector;
use Illuminate\Support\Collection;

/**
 * Manages the different Socialite authentication methods
 */
class SocialiteMethodManager
{
    public function __construct(public readonly AuthenticationMethodRepositoryContract $authenticationMethodRepository)
    {
    }

    public function getEnabledMethods(): Collection
    {
        $r = $this->authenticationMethodRepository->find(AuthenticationMethodSelector::usableMethod(SocialiteMethod::class));

        return $r;
    }

    /**
     * Return if at least one provider is usable
     *
     * @return bool
     */
    public function hasAtLeastOneUsableProvider(): bool
    {
        return $this->getEnabledMethods()->count() > 0;
    }
}