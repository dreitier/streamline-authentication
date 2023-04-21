<?php

namespace Dreitier\Streamline\Authentication\Methods\SelectableUser\Decorators;

use Dreitier\Streamline\Authentication\Controllers\Decorators\UiAuthenticationMethodDecoratorAdapter;
use Dreitier\Streamline\Authentication\Methods\SelectableUserMethod;
use Dreitier\Streamline\Authentication\Package;
use Illuminate\Contracts\View\View;

class SelectableUserUiDecorator extends UiAuthenticationMethodDecoratorAdapter
{
    public function __construct(protected int $order = 100)
    {
    }

    public function render(): View
    {
        $customResolver = Package::configWithDefault('methods.selectable_user.finder', null);
        $users = [];

        if (is_callable($customResolver)) {
            $users = $customResolver($users);
        } else {
            $users = Package::userQueryBuilder()->get()->mapWithKeys(function ($item, $key) {
                $principalProperty = Package::configWithDefault('methods.selectable_user.find_by_key', 'email');
                $displayNameProperty = Package::configWithDefault('methods.selectable_user.display_name_property', 'email');

                return [$item->$principalProperty => is_callable($displayNameProperty) ? $displayNameProperty($item) : $item->$displayNameProperty];
            });
        }

        return Package::view('selectable_user.form', [
            'users' => $users,
            'route' => route(Package::route('auth.predefined-email')),
        ]);
    }

    public function decorates(): bool
    {
        $r = $this->hasAuthenticationMethod(SelectableUserMethod::class);
        return $r;
    }
}
