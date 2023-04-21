<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Tests\Pipe;

use Dreitier\Piedpiper\Flow\Context;
use Dreitier\Piedpiper\Flow\NoResponseCreatedException;
use Dreitier\Piedpiper\Pipe;
use Dreitier\Piedpiper\Step\Contracts\Step;
use Dreitier\Streamline\Authentication\Tests\TestCase;
use Illuminate\Http\Response;


class StepWithoutAnyFollowingStep implements Step
{
    public function handle(Context $ctx, \Closure $next): Response
    {
        return $next();
    }
}

class PipeTest extends TestCase
{
    /**
     * @test
     */
    public function pipeWithoutResponse_throwsNoResponseCreatedException()
    {
        $this->expectException(NoResponseCreatedException::class);

        $pipe = new Pipe([
            StepWithoutAnyFollowingStep::class
        ]);

        $response = $pipe->run();
    }

    /**
     * @test
     */
    public function pipeCanBeCreatedWithArguments()
    {
        $args = ['a'];
        $pipe = new Pipe([]);
        $sut = $pipe->createFlow($args);

        $this->assertEquals($args, $sut->getBag());
    }


    /**
     * @test
     */
    public function stepCanReadArgument()
    {
        $isCalled = false;

        $args = ['a' => true];
        $sut = (new Pipe([
            function (Context $ctx, \Closure $next) use (&$isCalled) {
                $isCalled = $ctx->get('a');
                return $next();
            }
        ]))->fallbackResponseHandler(fn($f) => null);;

        $sut->run($args);
        $this->assertTrue($isCalled);
    }
}