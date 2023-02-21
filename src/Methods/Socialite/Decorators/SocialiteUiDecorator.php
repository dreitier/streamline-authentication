<?php

namespace Dreitier\Streamline\Authentication\Methods\Socialite\Decorators;

use Dreitier\Streamline\Authentication\Controllers\Decorators\UiAuthenticationMethodDecoratorAdapter;
use Dreitier\Streamline\Authentication\Methods\Socialite\SocialiteMethodManager;
use Dreitier\Streamline\Authentication\Methods\SocialiteMethod;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Contracts\View\View;

class SocialiteUiDecorator extends UiAuthenticationMethodDecoratorAdapter
{
    public function __construct(protected int $order = 30)
    {
    }

    public function triggersDefaultRedirection(): string|bool
    {
        if (null !== ($onlySocialiteAuthentication = $this->firstAuthenticationMethod(SocialiteMethod::class))) {
            return $onlySocialiteAuthentication->handover();
        }

        return false;
    }

    public function decorates(): bool
    {
        return app(SocialiteMethodManager::class)->hasAtLeastOneUsableProvider();
    }

    public function render(): View
    {
        $methods = $this->allAuthenticationMethods(SocialiteMethod::class);

        return Package::view('socialite.form', [
            'socialiteAuthenticationMethods' => $methods,
        ]);
    }
}
