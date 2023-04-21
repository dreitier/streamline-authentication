<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Tests\Pipe\Flow;

use Dreitier\Piedpiper\Flow;
use Dreitier\Piedpiper\Flow\Context;
use Dreitier\Piedpiper\Step\Intercept;
use Dreitier\Piedpiper\Step\Contracts\Step;
use Dreitier\Streamline\Authentication\Tests\TestCase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Assert as PHPUnit;

class UpsertAfterAuth implements Step
{
    public function handle(Context $ctx, \Closure $next)
    {
        $ctx->push('upsert_called');
        return $next();
    }
}

class Login implements Step
{
    public function handle(Context $ctx, \Closure $next)
    {
        $ctx->push('login_called');

        return \Illuminate\Support\Facades\Response::make("asdad");
    }
}

class FlowFaker extends Flow
{
    public function assertSharedContextData($key, $expectedValue)
    {
        $value = $this->context->expect($key);
        PHPUnit::assertEquals($expectedValue, $value);
    }
}

class FlowTest extends TestCase
{
    /**
     * @test
     */
    public function pipeCanStoreArguments()
    {
        $pipeFlow = new FlowFaker([
            UpsertAfterAuth::class,
            Login::class
        ]);

        $r = $pipeFlow->next();
        $pipeFlow->assertSharedContextData(UpsertAfterAuth::class, 'upsert_called');
        $pipeFlow->assertSharedContextData(Login::class, 'login_called');
        $this->assertInstanceOf(Response::class, $r);
    }

    /**
     * @test
     */
    public function interception_canShortcircuitTheFlow()
    {
        $pipeFlow = new FlowFaker([
            UpsertAfterAuth::class,
            (new class implements Step {
                use Intercept;

                public function intercept(Context $ctx): bool
                {
                    return $ctx->has(UpsertAfterAuth::class);
                }

                function handle(Context $ctx, \Closure $next)
                {
                    return \Illuminate\Support\Facades\Response::noContent(205);
                }
            }),
            Login::class
        ]);

        $result = $pipeFlow->next();
        $pipeFlow->assertSharedContextData(UpsertAfterAuth::class, 'upsert_called');
        $this->assertFalse($pipeFlow->getContext()->has(Login::class));
    }

    /**
     * @test
     */
    public function aStep_canReturnAnotherStep()
    {
        $pipeFlow = new FlowFaker([
            A::class,
            B::class,
        ], ['a' => false, 'b' => false], fn($f) => redirect('dummy'));

        $pipeFlow->next();

        $this->assertTrue($pipeFlow->getContext()->get('a'));
        $this->assertFalse($pipeFlow->getContext()->get('b'));
        $this->assertTrue($pipeFlow->getContext()->get('c'));
    }
}

class A implements Step
{
    public function handle(Context $ctx, \Closure $next)
    {
        $ctx->push(true, 'a');
        return C::class;
    }
}

class B implements Step
{
    public function handle(Context $ctx, \Closure $next)
    {
        $ctx->push(true, 'b');
        return $next();
    }
}

class C implements Step
{
    public function handle(Context $ctx, \Closure $next)
    {
        $ctx->push(true, 'c');
        return null;
    }
}