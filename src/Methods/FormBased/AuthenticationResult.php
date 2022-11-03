<?php

namespace Dreitier\Streamline\Authentication\Methods\FormBased;

class AuthenticationResult
{
    public function __construct(public readonly mixed $resolvedUser)
    {
    }
}
