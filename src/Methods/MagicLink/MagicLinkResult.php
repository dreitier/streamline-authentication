<?php

namespace Dreitier\Streamline\Authentication\Methods\MagicLink;

class MagicLinkResult
{
    public function __construct(public readonly mixed $user, public readonly string $magicLink)
    {
    }
}
