<?php

namespace Dreitier\Streamline\Authentication\Methods\FormBased\Decorators;

use Dreitier\Streamline\Authentication\Controllers\Decorators\UiAuthenticationMethodDecoratorAdapter;
use Dreitier\Streamline\Authentication\Methods\FormBasedMethod;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Contracts\View\View;

class FormBasedUiDecorator extends UiAuthenticationMethodDecoratorAdapter
{
    public function __construct(protected int $order = 10)
    {
    }

    public function render(): View
    {
        return Package::view('form_based.form', [
            'route' => route(Package::route('auth.form')),
            'form' => Package::config('methods.form_based.form'),
        ]);
    }

    public function decorates(): bool
    {
        return $this->hasAuthenticationMethod(FormBasedMethod::class);
    }
}
