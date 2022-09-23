<?php

namespace Datomatic\EnumCollection\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Datomatic\EnumCollection\EnumCollection
 */
class EnumCollection extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Datomatic\EnumCollection\EnumCollection::class;
    }
}
