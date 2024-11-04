<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Traits;

trait HasUniqueEnumCollections
{ 
    public function forgetClassCastCacheKey($key): bool {
        if (array_key_exists($key, $this->classCastCache)) {
            unset($this->classCastCache[$key]);
            return true;
        }
        return false;
    }
}