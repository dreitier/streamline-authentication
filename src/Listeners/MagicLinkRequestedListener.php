<?php

namespace Dreitier\Streamline\Authentication\Listeners;

use Dreitier\Streamline\Authentication\Events\MagicLinkRequested;
use Dreitier\Streamline\Authentication\Methods\MagicLink\Mailable\LoginWithMagicLinkMailable;
use Illuminate\Support\Facades\Mail;

class MagicLinkRequestedListener
{
    public function handle(MagicLinkRequested $event): void
    {
        Mail::to($event->recipient)->send(new LoginWithMagicLinkMailable($event->magicLinkResult));
    }
}