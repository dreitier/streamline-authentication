<?php

namespace Dreitier\Streamline\Authentication\Controllers\Decorators;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

interface UiAuthenticationMethodDecorator
{
    /**
     * Set the available authentication methods in the current context
     *
     * @param  Collection  $collection
     * @return mixed
     */
    public function setAuthenticationMethods(Collection $collection);

    /**
     * Order of viewable authentication method. Lower order number comes first.
     *
     * @return int
     */
    public function getOrder(): int;

    /**
     * Return a default redirection URL.
     * If available, it is used to automatically redirect the user to that URL if no no other authentication method has been configured.
     *
     * @return string|bool
     */
    public function triggersDefaultRedirection(): string|bool;

    /**
     * Is decorator active
     *
     * @return bool
     */
    public function decorates(): bool;

    /**
     * Create a new subview if decorator is active
     *
     * @return View
     */
    public function render(): View;
}
