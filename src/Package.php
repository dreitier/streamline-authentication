<?php

declare(strict_types=1);

namespace Dreitier\Streamline\Authentication;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

    private static ?Builder $userModelResolved = null;

    public static function userQueryBuilder(): Builder
    {
        if (static::$userModelResolved) {
            return static::$userModelResolved;
        }

        $clazzOrCallable = Package::config('user.query_builder');

        if (is_callable($clazzOrCallable)) {
            $clazzOrCallable = $clazzOrCallable();
        }

        if (is_string($clazzOrCallable)) {
            if (!class_exists($clazzOrCallable)) {
                throw new \Exception("Model class '" .$clazzOrCallable . "' does not exist for streamline-authentication.user.query_builder");
            }

            $clazzOrCallable = new $clazzOrCallable();
        }

        if ($clazzOrCallable instanceof Builder) {
            // accept
        } elseif ($clazzOrCallable instanceof Model) {
            $clazzOrCallable = $clazzOrCallable->query();
        } else {
            throw new \Exception("streamline-authentication.user.query_builder must be either a Eloquent Model or Builder instance and not " . gettype($clazzOrCallable));
        }

        return (static::$userModelResolved = $clazzOrCallable);
    }
}