<?php

namespace Dreitier\Streamline\Authentication\Controllers\Decorators;

use Dreitier\Streamline\Authentication\Package;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * This abstract the login page so that all available decorators are registered.
 */
class LoginPage implements UiAuthenticationMethodDecorator
{
    use AcceptsAuthenticationMethods;

    public function __construct(public readonly Collection $decorators)
    {
    }

    public function triggersDefaultRedirection(): bool|string
    {
        // if only one decorator is available
        if ($this->decorators->count() == 1) {
            // retrieve default redirection or false from that one
            return $this->decorators->first()->triggersDefaultRedirection();
        }

        return false;
    }

    public function getOrder(): int
    {
        // highest order
        return -1;
    }

    public function render(): View
    {
        // find active decorators and sort them by order
        $decorators = $this->decorators->where(fn ($item) => $item->decorates())->sortBy(function ($decorator, $key) {
            return $decorator->getOrder();
        });

        return Package::view('index', [
            'decorators' => $decorators,
        ]);
    }

    public function decorates(): bool
    {
        return true;
    }
}
