<?php

namespace Dreitier\Streamline\Authentication\Events;

use Dreitier\Streamline\Authentication\Methods\MagicLink\MagicLinkResult;
use Illuminate\Foundation\Events\Dispatchable;

class MagicLinkRequested
{
    use Dispatchable;

    public function __construct(public readonly string $recipient, public readonly MagicLinkResult $magicLinkResult)
    {
    }
}