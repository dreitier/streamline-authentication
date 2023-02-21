<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Events;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

class CreatedAutoLoginUrls
{
    /**
     * @param Collection<AutoLoginUrl> $autoLoginUrls
     * @param CreateAutoLoginUrls $request
     */
    public function __construct(public readonly Collection $autoLoginUrls, public readonly CreateAutoLoginUrls $request)
    {
    }
}
