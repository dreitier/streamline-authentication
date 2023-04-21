<?php

namespace Dreitier\Streamline\Authentication\Methods\MagicLink;

use Illuminate\Support\Collection;

class MagicLinkResult
{
    public function __construct(public readonly Collection $users, public readonly array $autoLoginUrls)
    {
    }
}
