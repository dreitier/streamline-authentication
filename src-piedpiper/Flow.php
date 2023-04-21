<?php

namespace Dreitier\Piedpiper;

use Dreitier\Piedpiper\Facades\Context as ContextFacade;
use Dreitier\Piedpiper\Facades\InvokableFactory;
use Dreitier\Piedpiper\Flow\Context;
use Dreitier\Piedpiper\Flow\NoResponseCreatedException;
use Dreitier\Piedpiper\Step\Intercept;
use Dreitier\Piedpiper\Step\Invokable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;


class Flow
{
    protected Invokable|null $invokable = null;
    protected array $steps = [];
    protected Context|null $context = null;
    protected \Closure|null $fallbackResponseHandler = null;
    protected array $bag = [];

    public function __construct(
        array         $steps = [],
        array         $bag = [],
        \Closure|null $fallbackResponseHandler = null,
    )
    {
        // we put everything in reverse so that any intercepting handler can be put onto the stack
        $this->steps = array_reverse($steps);

        $this->fallbackResponseHandler = $fallbackResponseHandler;
        $this->bag = $bag;
    }

    protected function refreshContext(Invokable $invocation): Context
    {
        return $this->context = ContextFacade::create(
            $invocation,
            $this->context?->bag() ?? $this->bag,
            $this->context?->depth() ?? 0,
        );
    }

    public function getBag(): mixed
    {
        return $this->bag;
    }

    public function getContext(): ?Context
    {
        return $this->context;
    }

    protected function moveToNextInvokable(): ?Invokable
    {
        $nextStep = array_pop($this->steps);

        if ($nextStep) {
            $this->invokable = InvokableFactory::build($nextStep);

            return $this->invokable;
        }

        return null;
    }

    protected function maybeIntercept(): array
    {
        $r = [];

        // find steps which have to be put on stack first
        for ($i = (sizeof($this->steps) - 1); $i >= 0; $i--) {
            $oneOfNextSteps = $this->steps[$i];

            if (is_string($oneOfNextSteps) && in_array(Intercept::class, class_uses_recursive($oneOfNextSteps))) {
                if ((new $oneOfNextSteps)->intercept($this)) {
                    $r[] = $oneOfNextSteps;
                    $this->steps[] = $oneOfNextSteps;
                }
            }
        }

        return $r;
    }

    protected function createFallbackResponse(): null|Response|RedirectResponse
    {
        $useFallBackResponseHandler = $this->fallbackResponseHandler;

        if (!$useFallBackResponseHandler) {
            $useFallBackResponseHandler = function ($ctx) {
                throw new NoResponseCreatedException();
            };
        }

        return ($useFallBackResponseHandler)($this);
    }

    private int $depth = 0;

    public function next(): null|Response|RedirectResponse
    {
        $this->maybeIntercept();

        /** @var Invokable $nextInvokable */
        $nextInvokable = $this->moveToNextInvokable();

        if (empty($nextInvokable)) {
            return $this->createFallbackResponse();
        }

        $context = $this->refreshContext($nextInvokable);

        $forwardMethod = $nextInvokable->forwardMethod;

        $invokableResponse = $forwardMethod($context, function () {
            return $this->next();
        });

        if (InvokableFactory::accepts($invokableResponse)) {
            $this->getContext()->in();
            $this->steps[] = InvokableFactory::build($invokableResponse);
            $r = $this->next();
            $this->getContext()->out();
            return $r;
        }

        return $invokableResponse;
    }
}