<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication\Tests;

use Dreitier\Streamline\Authentication\Contracts\AuthenticationMethod;
use Dreitier\Streamline\Authentication\Methods\Factories\AuthenticationMethodFactory;
use Dreitier\Streamline\Authentication\Methods\FormBasedMethod;
use Dreitier\Streamline\Authentication\Methods\SocialiteMethod;
use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\AuthenticationMethodRepository;
use Dreitier\Streamline\Authentication\Repositories\Contracts\AuthenticationMethodSelector;

class AuthenticationMethodRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function anAuthenticationMethodWithoutFactory_isCreatedByDefaultFactory()
    {
        $ns = Package::key('methods');

        config([$ns => [
            'credentials_based_auth' => [
                'impl' => FormBasedMethod::class,
                'enabled' => true,
            ],
        ]]);

        $factoryMap = Package::config('authentication.factories');
        $factory = new AuthenticationMethodFactory($factoryMap);

        $sut = new AuthenticationMethodRepository($factory);
        $r = $sut->find(AuthenticationMethodSelector::all());

        $this->assertEquals(1, $r->count());
        /** @var AuthenticationMethod $first */
        $first = $r->first();

        $this->assertTrue($first->isEnabled());
        $this->assertTrue($first->isConfigured());
        $this->assertNotNull($first->getProvider());
        $this->assertTrue($first->getProvider()->isGlobal());
    }

    /**
     * @test
     */
    public function whenSocialiteIsConfigured_itFindsSocialiteMethods()
    {
        $ns = Package::key('methods');

        config([$ns => [
            'azure' => [
                'socialite' => [
                    'client_id' => 555,
                    'client_secret' => 666,
                ],
                'impl' => SocialiteMethod::class,
            ],
            'azure-2' => [
                'socialite' => [
                    'driver' => 'azure',
                    'client_id' => 777,
                    'client_secret' => 888,
                ],
                'impl' => SocialiteMethod::class,
            ],
        ]]);

        $factoryMap = Package::config('authentication.factories');
        $factory = new AuthenticationMethodFactory($factoryMap);

        $sut = new AuthenticationMethodRepository($factory);
        $r = $sut->find(AuthenticationMethodSelector::all());

        $this->assertEquals(2, $r->count());
        /** @var SocialiteMethod $first */
        $first = $r->first();

        $this->assertTrue($first->isConfigured());

        $driverForFirst = $first->driver();
        $this->assertNotNull($driverForFirst);
        $content = $driverForFirst->redirect();

        $this->assertMatchesRegularExpression('/client_id=555/', $content->getContent());
    }
}
