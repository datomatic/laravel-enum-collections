<?php

namespace Datomatic\EnumCollections;

use BackedEnum;
use Illuminate\Support\Collection;
use UnitEnum;

/**
 * @extends Collection<int,UnitEnum>
 */
class EnumCollection extends Collection
{
    public function contains($key, $operator = null, $value = null)
    {
        if($this->count()){
            $enumClass = get_class($this->first());
            $enum = self::tryGetEnumFromValue($key, $enumClass);

            return in_array($enum, $this->items);
        }

        return false;
    }

    public static function tryGetEnumFromValue(mixed $value, mixed $enumClass): ?UnitEnum
    {
        if ($value instanceof UnitEnum) {
            return $value;
        }

        if(is_string($value)) {
            foreach ($enumClass::cases() as $case) {
                if ($case->name === $value) {
                    return $case;
                }
            }
        }

        if (is_subclass_of($enumClass, BackedEnum::class)) {
            return $enumClass::tryFrom(intval($value)) ?? $enumClass::tryFrom($value);
        }

        return null;
    }
}
