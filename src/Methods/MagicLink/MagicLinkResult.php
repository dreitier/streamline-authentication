<?php

namespace Dreitier\Streamline\Authentication\Methods\MagicLink;

use Dreitier\Streamline\Authentication\Events\CreatedAutoLoginUrls;
use Illuminate\Support\Collection;

class MagicLinkResult
{
    public function __construct(public readonly Collection $users, public readonly CreatedAutoLoginUrls $createdAutoLoginUrls)
    {
    }

    public function autoLoginUrls(): Collection
    {
        return $this->createdAutoLoginUrls->autoLoginUrls;
    }
}
