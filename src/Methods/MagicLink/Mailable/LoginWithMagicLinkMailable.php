<?php

namespace Dreitier\Streamline\Authentication\Methods\MagicLink\Mailable;

use Dreitier\Streamline\Authentication\Methods\MagicLink\MagicLinkResult;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginWithMagicLinkMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly MagicLinkResult $magicLinkResult)
    {
    }

    public function content()
    {
        return new Content(
            markdown: Package::viewKey('magic_link.email.login'),
            with: [
                'h1' => $this->getSubject(),
                'users' => $this->magicLinkResult->users,
                'autoLoginUrls' => $this->magicLinkResult->autoLoginUrls,
            ],
        );
    }

    public function getSubject()
    {
        return 'Log in with magic link';
    }

    public function envelope()
    {
        return new Envelope(
            subject: $this->getSubject()
        );
    }
}
