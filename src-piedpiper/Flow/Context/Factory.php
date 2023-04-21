<?php

namespace Dreitier\Piedpiper\Flow\Context;

use Dreitier\Piedpiper\Step\Invokable;
use Dreitier\Piedpiper\Flow\Context;

class Factory
{
    public function create(Invokable $invocation, array $bag, int $depth): Context
    {
        return new Context(
            $invocation,
            $bag,
            $depth,
        );
    }
}