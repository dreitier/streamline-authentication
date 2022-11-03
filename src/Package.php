<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication;

use Illuminate\View\View;

class Package
{
    const CONFIG_NAMESPACE = 'streamline-authentication';

    public static function key(?string $key = null): string
    {
        $r = self::CONFIG_NAMESPACE.($key ? '.'.$key : '');

        return $r;
    }

    public static function config(?string $key = null): mixed
    {
        $key = self::key($key);

        return config($key);
    }

    public static function configWithDefault(string $key, $default = null)
    {
        return config(self::key($key), $default);
    }

    public static function viewKey($name): string
    {
        return self::CONFIG_NAMESPACE.'::'.$name;
    }

    public static function view($name, ...$args): View
    {
        return view(self::viewKey($name), ...$args);
    }

    public static function userModel(): string
    {
        return Package::config('user.impl');
    }
}
