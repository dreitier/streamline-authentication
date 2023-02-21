<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication;

use Illuminate\View\View;

class Package
{
    const CONFIG_NAMESPACE = 'streamline-authentication';

    public static function key(?string $key = null): string
    {
        $r = self::CONFIG_NAMESPACE . ($key ? '.' . $key : '');

        return $r;
    }

    public static function route(string $name): string
    {
        return self::key($name);
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
        return self::CONFIG_NAMESPACE . '::' . $name;
    }

    public static function view($name, ...$args): View
    {
        return view(self::viewKey($name), ...$args);
    }

    public static function lambdaOrInvokable(string $key, callable $default)
    {
        $r = self::config($key);

        if (!$r) {
            $r = $default;
        }

        if (is_string($key) && class_exists($key)) {
            $invokable = new $r();

            if (is_callable($invokable)) {
                $r = $invokable;
            }
        }

        return $r;
    }

    private static ?string $userModelResolved = null;

    public static function userModel(): string
    {
        if (static::$userModelResolved) {
            return static::$userModelResolved;
        }

        $clazz = Package::config('user.impl');

        if (!class_exists($clazz)) {
            throw new \Exception("Class '" . $clazz . "' does not exist as a user model. Change .user.model in streamline-authentication.conf");
        }

        return (static::$userModelResolved = $clazz);
    }
}