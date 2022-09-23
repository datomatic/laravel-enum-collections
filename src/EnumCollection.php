<?php

namespace Datomatic\EnumCollection;

use BackedEnum;
use Illuminate\Support\Collection;
use UnitEnum;

/**
 * @extends Collection<int,UnitEnum>
 */
class EnumCollection extends Collection
{
    public function contains($key, $operator = null, $enum = null)
    {
        if ($this->isEmpty()) {
            return false;
        }

        if (! $enum instanceof UnitEnum && $this->first()) {
            $enumClass = get_class($this->first());

            if (is_subclass_of($enumClass, BackedEnum::class) &&
                (is_string($enum) || is_int($enum))
            ) {
                $enum = $enumClass::tryFrom($enum);
            }

            if (! $enum && is_string($enum)) {
                foreach ($enumClass::cases() as $case) {
                    if ($case->name === $enum) {
                        $enum = $case;
                        break;
                    }
                }
            }
        }

        return parent::contains($key, $operator, $enum);
    }
}
