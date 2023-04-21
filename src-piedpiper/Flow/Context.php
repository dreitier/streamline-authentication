<?php

namespace Dreitier\Piedpiper\Flow;

use Dreitier\Piedpiper\Flow\Context\MissingBagPropertyException;
use Dreitier\Piedpiper\Step\Invokable;
use Illuminate\Support\Facades\Log;


class Context
{
    private array $bag = [];
    private int $depth = 0;

    public function __construct(
        public readonly Invokable $activeStep,
        array                     $bag = [],
        int                       $depth = 0,
    )
    {
        $this->bag = $bag;
        $this->depth = $depth;
    }

    public function depth(): int
    {
        return $this->depth;
    }

    public function in(): Context
    {
        $this->depth++;
        return $this;
    }

    public function out(): Context
    {
        $this->depth = $this->depth > 0 ? $this->depth-- : 0;
        return $this;
    }

    public function info($message, array $context = []): Context
    {
        Log::info(str_repeat("--", $this->depth * 2) . " " . $message, $context);
        return $this;
    }

    public function push(mixed $value, string $key = null): Context
    {
        $this->bag[$key ?? (is_object($value) ? get_class($value) : $this->activeStep->name)] = $value;
        return $this;
    }

    public function next(mixed $nextStep, mixed $push = null): mixed
    {
        if ($push) {
            $this->push($push);
        }

        return $nextStep;
    }

    public function has(string $key)
    {
        return isset($this->bag[$key]);
    }

    public function expect(string $key)
    {
        if (!$this->has($key)) {
            throw new MissingBagPropertyException("Pipe context argument '$key' is missing");
        }

        return $this->get($key);
    }

    public function get(string $key): mixed
    {
        return $this->bag[$key];
    }

    public function remove(string $key)
    {
        unset($this->bag[$key]);
    }

    public function bag(): array
    {
        return $this->bag;
    }
}