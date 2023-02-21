<?php

namespace Dreitier\Streamline\Authentication\Util\Collection;

use Dreitier\Streamline\Authentication\Package;

class UserCollection extends \Illuminate\Support\Collection
{
    public function __construct($items = [])
    {
        foreach ($items as $item) {
            self::checkUserModel($item);
        }

        parent::__construct($items);
    }

    public static function of(array $items): UserCollection
    {
        return new static($items);
    }

    public static function checkUserModel(object $item)
    {
        $impl = Package::config('user.impl');

        if (!(is_a($item, $impl))) {
            throw new \Exception("Item is not of type " . $impl . " but of " . get_class($item));
        }
    }
}