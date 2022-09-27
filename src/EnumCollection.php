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
        $firstEnum = $this->first();
        if ($firstEnum) {
            $enumClass = get_class($firstEnum);
            $enum = self::tryGetEnumFromValue($key, $enumClass);

            return in_array($enum, $this->items);
        }

        return false;
    }

    public static function tryGetEnumFromValue(mixed $value, string $enumClass): ?UnitEnum
    {
        if ($value instanceof UnitEnum) {
            return $value;
        }

        if (is_string($value) && method_exists($enumClass, 'cases')) {
            foreach ($enumClass::cases() as $case) {
                if ($case->name === $value) {
                    return $case;
                }
            }
        }

        if (is_subclass_of($enumClass, BackedEnum::class)) {
            if (is_string($enumClass::cases()[0]->value) && is_string($value)) {
                return $enumClass::tryFrom($value);
            } else {
                return $enumClass::tryFrom(intval($value));
            }
        }

        return null;
    }
}
