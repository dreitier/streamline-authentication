<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Providers;

use Dreitier\Streamline\Authentication\Contracts\Provider as ProviderContract;

class Provider implements ProviderContract
{
    const GLOBAL_PROVIDER_ID = 'default';

    public static function globalProvider(string $uniqueFlowHandlerName)
    {
        return new Provider($uniqueFlowHandlerName);
    }

    public static function create(string $uniqueFlowHandlerName, mixed $id = null)
    {
        return new Provider($uniqueFlowHandlerName, ($id == self::GLOBAL_PROVIDER_ID ? null : $id));
    }

    public function __construct(public readonly string $type, public readonly mixed $id = null)
    {
        throw_if($id == self::GLOBAL_PROVIDER_ID, \Exception::class, 'You can not set the id to the default global ID');
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isGlobal(): bool
    {
        return $this->id === null;
    }

    public function getId(): mixed
    {
        return $this->id ?? self::GLOBAL_PROVIDER_ID;
    }

    public function __toString()
    {
        return $this->type.':'.$this->getId();
    }
}
