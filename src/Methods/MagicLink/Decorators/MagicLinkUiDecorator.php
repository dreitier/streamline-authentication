<?php

namespace Dreitier\Streamline\Authentication\Methods\MagicLink\Decorators;

use Dreitier\Streamline\Authentication\Controllers\Decorators\AcceptsAuthenticationMethods;
use Dreitier\Streamline\Authentication\Controllers\Decorators\UiAuthenticationMethodDecoratorAdapter;
use Dreitier\Streamline\Authentication\Methods\MagicLinkMethod;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Contracts\View\View;

class MagicLinkUiDecorator extends UiAuthenticationMethodDecoratorAdapter
{
    use AcceptsAuthenticationMethods;

    public function __construct(protected int $order = 20)
    {
    }

    public function render(): View
    {
        return Package::view('magic_link.form', [
            'route' => route(Package::route('auth.magic-link.request')),
        ]);
    }

    public function decorates(): bool
    {
        return $this->hasAuthenticationMethod(MagicLinkMethod::class);
    }
}
