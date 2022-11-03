<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Providers;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationContext;
use Illuminate\Support\Collection;

class Providers extends Collection
{
    public function __construct(public readonly string $contextClazz, public readonly mixed $parent)
    {
    }

    public function createContext(AuthenticationContext $context)
    {
        $clazz = $this->contextClazz;

        return new $clazz($context->provider, $this->parent, $context->configuration);
    }

    public function createContextForFirst()
    {
        return $this->createContext($this->first());
    }
}
