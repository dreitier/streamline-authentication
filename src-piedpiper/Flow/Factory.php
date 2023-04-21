<?php

namespace Dreitier\Piedpiper\Flow;

use Dreitier\Piedpiper\Flow;

class Factory
{
    public function create(array $constructorArgs): Flow
    {
        return new Flow(... $constructorArgs);
    }
}