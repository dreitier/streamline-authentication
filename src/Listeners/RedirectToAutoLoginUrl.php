<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Listeners;

use Dreitier\Streamline\Authentication\Events\AuthenticationSucceeded;
use Dreitier\Streamline\Authentication\Events\CreateAutoLoginUrls;
use Dreitier\Streamline\Authentication\Events\CreatedAutoLoginUrls;

class RedirectToAutoLoginUrl
{
    public function handle(AuthenticationSucceeded $authenticationSucceeded)
    {
        /** @var CreatedAutoLoginUrls $createdAutoLoginUrls */
        $createdAutoLoginUrls = get_first_event_response(event(new CreateAutoLoginUrls($authenticationSucceeded->users, [])));

        return redirect($createdAutoLoginUrls->autoLoginUrls->first()->create());
    }
}
