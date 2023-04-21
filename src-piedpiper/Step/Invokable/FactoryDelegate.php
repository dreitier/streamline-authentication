<?php

namespace Dreitier\Piedpiper\Step\Invokable;

use Dreitier\Piedpiper\Step\Contracts\Step;
use Dreitier\Piedpiper\Step\InstantiationException;
use Dreitier\Piedpiper\Step\Invokable\Contracts\Factory as InvokableFactory;
use Illuminate\Support\Str;
use Dreitier\Piedpiper\Step\Invokable;

class FactoryDelegate implements InvokableFactory
{
    private array $factories = [];

    public function __construct(array $factories = null)
    {
        $this->factories = $factories ?? $this->createDefaultFactories();
    }

    protected function invokableInstanceFactory(): InvokableFactory
    {
        return (new class implements InvokableFactory {

            function accepts(mixed $someInvokable): bool
            {
                return ($someInvokable instanceof Invokable);
            }

            function build(mixed $someInvokable): Invokable
            {
                return $someInvokable;
            }
        });
    }

    protected function callableInvokableFactory(): InvokableFactory
    {
        return (new class implements InvokableFactory {

            function accepts(mixed $someInvokable): bool
            {
                return is_callable($someInvokable);
            }

            function build(mixed $someInvokable): Invokable
            {
                $forwardMethod = $someInvokable;
                $name = "";
                if ($someInvokable instanceof \Closure) {
                    $name = (string)Str::uuid();
                } else {
                    $name = "" . $someInvokable->__toString();
                }

                return new Invokable($name, $forwardMethod);
            }
        });
    }

    public function classOrInstanceInvokableFactory(): InvokableFactory
    {
        return (new class implements InvokableFactory {
            function accepts(mixed $someInvokable): bool
            {
                return is_string($someInvokable) || (is_object($someInvokable) && ($someInvokable instanceof Step));
            }

            function build(mixed $someInvokable): Invokable
            {
                $someInvokableInstance = is_string($someInvokable) ? new $someInvokable : $someInvokable;

                if (!($someInvokableInstance instanceof Step)) {
                    var_dump($someInvokableInstance);
                    throw new InstantiationException("Step definition does not implement Step interface");
                }

                $forwardMethod = function ($ctx, $next) use ($someInvokableInstance) {
                    return $someInvokableInstance->handle($ctx, $next);
                };
                $name = get_class($someInvokableInstance);

                return new Invokable($name, $forwardMethod);
            }
        });
    }

    public function createDefaultFactories()
    {
        return [
            $this->invokableInstanceFactory(),
            $this->callableInvokableFactory(),
            $this->classOrInstanceInvokableFactory(),
        ];
    }

    public function accepts(mixed $someInvokable): bool
    {
        return $this->getFactoryFor($someInvokable) != null;
    }

    public function build(mixed $someInvokable): Invokable
    {
        $factory = $this->getFactoryFor($someInvokable);

        if (!$factory) {
            throw new InstantiationException("Step definition can not be used for an active step");
        }

        return $factory->build($someInvokable);
    }

    public function getFactoryFor($stepDefinition): ?InvokableFactory
    {
        foreach ($this->factories as $factory) {
            if ($factory->accepts($stepDefinition)) {
                return $factory;
            }
        }

        return null;
    }
}