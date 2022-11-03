# Streamline Authentication for Laravel - dreitier/streamline-authentication
### *Common authentication methods for multi-tenant Laravel apps.*

## Features

- Provide common authentication methods
  - Form-based logins with principal (e.g. username, email) and password
  - Password-less logins by sending emails with a magic login link
  - Logins by selecting pre-defined users for demo/e2e environments
  - Socialite integration for OAuth
- Targeted for developers of multi-tenant Laravel SaaS/on-premises applications
  - Activation of different authentication methods for only specific groups of customers
  - Configurable authentication methods per tenant
  - Users can log in through different Socialite drivers in the same tenant

## When to go with Streamline Authentication instead of Laravel Fortify?
For the most Laravel applications, [Fortify](https://laravel.com/docs/9.x/fortify) implements all required authentication workflows in a much easier and Laravel-esk way. If you need password-based authentication with optional 2FA enabled, and a registration and password recovery workflow you should go probably go with Fortify.
If you need a more configurable and dynamic way of enabling and configuring different authentication methods throughout different tenants, *Streamline Authentication* might be an option for you.

## Installation

Install the composer package and publish the required configuration:
```bash
composer require dreitier/streamline-authentication

php artisan vendor:publish --provider=Dreitier\\Streamline\\Authentication\\StreamlineAuthenticationServiceProvider --tag=config
```

### Custom views
If you want to customize the views and mails, publish the assets with
```bash
php artisan vendor:publish --provider=Dreitier\\Streamline\\Authentication\\StreamlineAuthenticationServiceProvider --tag=assets
```

You find the customized assets below `resources/views/vendor/streamline-authentication`.

### Custom routes
By default, *Streamline Authentication* registers the authentication method flows at the `/sign-in` endpoint. Only enabled authentication methods are registered.

If you want to publish the routes by yourself, use

```bash
php artisan vendor:publish --provider=Dreitier\\Streamline\\Authentication\\StreamlineAuthenticationServiceProvider --tag=routes
```

and set in `config/streamline-authentication.php`:

```php
return [
    // ...
    'routes' => false
    // ...
];
```

## Configuration
`config/streamline-authentication.php` contains annotated configuration options.

## Terminology

| Term | Description                                                                |
| --- |----------------------------------------------------------------------------|
| Authentication method | Like form-based authentication, Socialite etc.                             |
| Authentication provider | Provides the configuration for an authentication method of a given backend |

## FAQ
