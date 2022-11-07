<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Tests\Methods\Enablement;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Contracts\Provider as ProviderContract;
use Dreitier\Streamline\Authentication\Methods\Enablement\BooleanEnabler;
use Dreitier\Streamline\Authentication\Methods\Enablement\EnablementFactory;
use Dreitier\Streamline\Authentication\Methods\Factories\AuthenticationMethodFactory;
use Dreitier\Streamline\Authentication\Methods\FormBasedMethod;
use Dreitier\Streamline\Authentication\Methods\SocialiteMethod;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\AuthenticationMethodRepository;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodSelector;
use Dreitier\Streamline\Authentication\Tests\TestCase;

class EnablementFactoryTest extends TestCase
{
    private ?EnablementFactory $sut = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new EnablementFactory();
    }

    /**
     * @test
     */
    public function aBoolean_createsABooleanEnabler()
    {
        $r = $this->sut->create(true);

        $this->assertTrue($r instanceof BooleanEnabler);
        $this->assertTrue($r->isEnabled());
    }

    /**
     * @test
     * @return void
     */
    public function anAuthenticationMethod_delegatesToIsEnabled()
    {
        $r = $this->sut->create(new AuthenticationMethodDummy());
        $this->assertTrue($r->isEnabled());
    }

    /**
     * @test
     * @return void
     */
    public function aRule_willBeDelegated() {
        config(['app' => ['env' => 'a']]);
        $rule = 'in_environment:unit_test|other_ignored_condition';
        $r = $this->sut->create($rule);
        $this->assertFalse($r->isEnabled());

        config(['app' => ['env' => 'unit_test']]);
        $r = $this->sut->create($rule);
        $this->assertTrue($r->isEnabled());
    }
}


class AuthenticationMethodDummy implements AuthenticationMethod
{

    public function isConfigured(): bool
    {
        // TODO: Implement isConfigured() method.
    }

    public function getProvider(): ?ProviderContract
    {
        // TODO: Implement getProvider() method.
    }

    public function isEnabled(): bool
    {
        return true;
    }
}
