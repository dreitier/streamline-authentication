<?php

namespace Dreitier\Piedpiper;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Dreitier\Piedpiper\Facades\Flow as FlowFactory;

class Pipe
{
    public function __construct(public readonly array $steps = [])
    {
    }

    public function run(array $bag = []): null|Response|RedirectResponse
    {
        return $this->createFlow($bag)->next();
    }

    public function createFlow(array $bag = []): Flow
    {
        return $this->create($bag);
    }

    private \Closure|null $fallbackResponseHandler = null;

    public function fallbackResponseHandler(\Closure $fallbackResponseHandler): Pipe
    {
        $this->fallbackResponseHandler = $fallbackResponseHandler;
        return $this;
    }

    protected function create(array $bag = []): Flow
    {
        return FlowFactory::create([
            'steps' => $this->steps,
            'bag' => $bag,
            'fallbackResponseHandler' => $this->fallbackResponseHandler,
        ]);
    }
}