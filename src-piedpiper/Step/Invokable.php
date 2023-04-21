<?php

namespace Dreitier\Piedpiper\Step;

class Invokable
{
    public function __construct(public readonly string $name, public readonly \Closure $forwardMethod)
    {
    }
}