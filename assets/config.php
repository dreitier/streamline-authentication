<?php

declare(strict_types=1);

use Dreitier\Streamline\Authentication\Facades\StreamlineAuthenticationMethod;
use Dreitier\Streamline\Authentication\Methods\Factories\ConfigurableAuthenticationMethodFactory;
use Dreitier\Streamline\Authentication\Methods\FormBased\Decorators\FormBasedUiDecorator;
use Dreitier\Streamline\Authentication\Methods\MagicLink\Decorators\MagicLinkUiDecorator;
use Dreitier\Streamline\Authentication\Methods\SelectableUser\Decorators\SelectableUserUiDecorator;
use Dreitier\Streamline\Authentication\Methods\Socialite\Decorators\SocialiteUiDecorator;
use Dreitier\Streamline\Authentication\Methods\Socialite\Drivers\AzureDriverAdapter;
use Dreitier\Streamline\Authentication\Methods\Socialite\Factories\SocialiteAuthenticationMethodFactory;
use Dreitier\Streamline\Authentication\Repositories\DelegateAuthenticationMethodRepository;
use Dreitier\Streamline\Authentication\Repositories\Strategies\RepositoryLocatorStrategy;
use Dreitier\Streamline\Authentication\Tenancy\TenantContextProvider;

return [
    'user' => [
        /**
         * The user model when retrieving a person from the repository
         */
        'impl' => \App\Models\User::class,
    ],
    'methods' => [
        /*
        |--------------------------------------------------------------------------
        | Form-based
        |--------------------------------------------------------------------------
        |
        | Form-based authentication with a principal (e.g. username, email) and a password.
        */
        'form_based' => [
            'impl' => \Dreitier\Streamline\Authentication\Methods\FormBasedMethod::class,
            'form' => [
                'principal' => [
                    /**
                     * Name of principal input field.
                     */
                    'name' => 'principal',
                    'label' => 'Principal',
                ],
                'password' => [
                    /**
                     * Name of password input field.
                     */
                    'name' => 'password',
                    'label' => 'Password',
                ],
            ],
            /**
             * Find the user behind the principal by this key/column.
             * If your users have to log in with an email address, you have to use the `email` database column.
             */
            'find_by_key' => 'email',
            /**
             * Just for demonstration purposes. By default, all registered authentication methods are enabled.
             * You can also provide a custom callback function for the authentication method.
             */
            'enabled' => 'in_environment:dev,e2e,local,demo',
            /**
             * Not yet
             */
            'valid_email_domains' => [],
        ],
        /*
        |--------------------------------------------------------------------------
        | Selectable user
        |--------------------------------------------------------------------------
        |
        | Provide the end user a list of selectable user with which the end user can log in. No password is required.
        | This should be only used in non-production environments, like demo environments or test environments.
        */
        'selectable_user' => [
            'impl' => \Dreitier\Streamline\Authentication\Methods\SelectableUserMethod::class,
            /**
             * Enable this authentication method only if we are in APP_ENV=dev, APP_ENV=e2e, APP_ENV=local or APP_ENV=demo.
             * Even if you can pass Lambdas as value, don't use it. PHP/Laravel can not serialize that argument. Use any of the helper methods available, like `in_environment`.
             */
            'enabled' => 'in_environment:dev,e2e,local,demo',
            /**
             * Find the user behind the principal by this key/column.
             * If your users have to log in with an email address, you have to use the `email` database column.
             */
            'find_by_key' => 'email',
            /**
             * Model property to use for showing the name.
             *
             * You can also specify a callback with `callback($user): string`:
             * `fn($user) => $user->name`
             */
            'display_name_property' => 'email',
        /**
         * A custom find method. By default, all users from the user model defined above are selected.
         * This callback must return a hashmap [$principal => $displayName]:
         * `fn() => ['id_1' => 'display_name_1', 'id_2' => 'display_name_2']`
         */
            // 'finder' => null,
        ],
        /*
        |--------------------------------------------------------------------------
        | Password-less authentication.
        |--------------------------------------------------------------------------
        |
        | The user has just to enter his or her email address and will automatically receive an email with a link.
        | You have to configure your Laravel email providers properly.
        */
        'magic_link_via_email' => [
            'impl' => \Dreitier\Streamline\Authentication\Methods\MagicLinkMethod::class,
            /**
             * Timeout in seconds after the link expires.
             */
            'expires_after_seconds' => 600,
            /**
             * Use this model attribute to find the given user
             */
            'find_by_key' => 'email',
            /**
             * Use this model attribute to pass it to the generated magic link.
             * You can also use a `callable(User $user)` to define some custom logic.
             */
            'route_principal_attribute' => 'id',
            /**
             * If you need some custom logic to resolve the user from a route principal,
             * you can use the `callable($principal)` callback
             */
            'resolve_user_from_route_principal' => null,
            'enabled' => 'in_environment:dev,e2e,local,demo',
        ],
        /*
        |--------------------------------------------------------------------------
        | Socialite
        |--------------------------------------------------------------------------
        |
        | When using Socialite, you have to re-register each Socialite driver (like Twitter or Microsoft Azure).
        |
        */
        'azure' => [
            'impl' => \Dreitier\Streamline\Authentication\Methods\SocialiteMethod::class,
            'socialite' => [
                /**
                 * Socialite's driver name
                 */
                'driver' => 'azure',
                'client_id' => env('AZURE_AD_APPLICATION_ID'),
                'client_secret' => env('AZURE_AD_APPLICATION_SECRET'),
            ],
            /**
             * Provide some specific behaviour for the Socialite driver.
             * If your driver does not have any specific adapter, you can omit that configuration property.
             */
            'adapter' => AzureDriverAdapter::class,
            'enabled' => true,
        ],
    ],
    'ui' => [
        /**
         * Decorate the login page with those classes.
         * Basically, each authentication method can extend the login page through decorators.
         */
        'decorators' => [
            SocialiteUiDecorator::class,
            FormBasedUiDecorator::class,
            SelectableUserUiDecorator::class,
            MagicLinkUiDecorator::class,
        ],
    ],
    'authentication' => [
        /**
         * Repository for finding valid authentication methods.
         */
        'repository' => [
            /**
             * Strategy to use to locate an authentication method repository.
             * This is practically used to allow multi-tenant environments to have different authentication methods.
             */
            'strategy' => RepositoryLocatorStrategy::class,
            /**
             * Delegate to another authentication method repository based upon the strategy.
             */
            'impl' => DelegateAuthenticationMethodRepository::class,
        ],
        /**
         * Factories for creating new authentication methods
         */
        'factories' => [
            /**
             * Socialite has a custom factory to configure the proper Socialite drivers.
             */
            \Dreitier\Streamline\Authentication\Methods\SocialiteMethod::class => SocialiteAuthenticationMethodFactory::class,
            /**
             * By default, any other authentication method is created by this factory.
             */
            'default' => ConfigurableAuthenticationMethodFactory::class,
        ],
    ],
    'routes' => true,
    'tenancy' => [
        'provider' => [
            'class' => TenantContextProvider::class,
        ],
    ],
];
