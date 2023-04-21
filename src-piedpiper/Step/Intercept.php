<?php

namespace Dreitier\Piedpiper\Step;

use Dreitier\Piedpiper\Flow\Context;

trait Intercept
{
    public function intercept(Context $ctx): bool
    {
        return false;
    }
}