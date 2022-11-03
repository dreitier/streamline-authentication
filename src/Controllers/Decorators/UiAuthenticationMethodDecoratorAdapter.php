<?php

namespace Dreitier\Streamline\Authentication\Controllers\Decorators;

abstract class UiAuthenticationMethodDecoratorAdapter implements UiAuthenticationMethodDecorator
{
    use AcceptsAuthenticationMethods;

    protected int $order = 10;

    protected bool $triggersDefaultRedirection = false;

    public function getOrder(): int
    {
        return $this->order;
    }

    public function triggersDefaultRedirection(): string|bool
    {
        return $this->triggersDefaultRedirection;
    }
}
