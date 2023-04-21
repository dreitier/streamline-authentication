<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Steps;

use Dreitier\Piedpiper\Flow\Context;
use Dreitier\Piedpiper\Step\Contracts\Step;
use Dreitier\Streamline\Authentication\Events\Login;
use Dreitier\Streamline\Authentication\Facades\AutoLoginUrlFactory;
use Dreitier\Streamline\Authentication\Util\Collection\UserCollection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RedirectToAutoLoginUrlStep implements Step
{
    public function handle(Context $ctx, \Closure $next): Response|RedirectResponse
    {
        /** @var Login $login */
        if (!($login = $ctx->get(Login::class))) {
            return $next();
        }

        $ctx->info("Redirecting to central login URL...");

        $urls = AutoLoginUrlFactory::create(UserCollection::of($login->user));

        return redirect($urls[0]->create());
    }

    public static function doNext(mixed $user, Context $context) {
        return $context->next(static::class, new Login($user));
    }
}