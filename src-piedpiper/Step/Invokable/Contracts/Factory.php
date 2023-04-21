<?php

namespace Dreitier\Piedpiper\Step\Invokable\Contracts;

use Dreitier\Piedpiper\Step\Invokable;

interface Factory
{
    public function accepts(mixed $someInvokable): bool;

    public function build(mixed $someInvokable): Invokable;
}