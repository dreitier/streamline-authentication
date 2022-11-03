<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Tests;

use Dreitier\Streamline\Authentication\StreamlineAuthenticationServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            StreamlineAuthenticationServiceProvider::class,
            \SocialiteProviders\Manager\ServiceProvider::class,
        ];
    }
}
