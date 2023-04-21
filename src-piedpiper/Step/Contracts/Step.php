<?php

namespace Dreitier\Piedpiper\Step\Contracts;

use Dreitier\Piedpiper\Flow\Context;

interface Step
{
    public function handle(Context $ctx, \Closure $next);
}