<?php

namespace Datomatic\EnumCollections\Casts;

use BackedEnum;
use Datomatic\EnumCollections\EnumCollection;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use UnitEnum;

class AsEnumCollection implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @template TEnum of UnitEnum|BackedEnum
     *
     * @param array{class-string<TEnum>} $arguments
     * @return \Illuminate\Contracts\Database\Eloquent\CastsAttributes<\Illuminate\Support\Collection<array-key, TEnum>, iterable<TEnum>>
     */
    public static function castUsing(array $arguments)
    {
        return new class($arguments) implements CastsAttributes {
            protected $arguments;

            public function __construct(array $arguments)
            {
                $this->arguments = $arguments;
            }


            public function get($model, $key, $value, $attributes)
            {
                if (!isset($attributes[$key]) || is_null($attributes[$key])) {
                    return;
                }

                $data = Json::decode($attributes[$key]);

                if (!is_array($data)) {
                    return;
                }

                $enumClass = $this->arguments[0];

                return EnumCollection::tryFrom($data, $enumClass);
            }

            public function set($model, $key, $value, $attributes)
            {
                $value = $value !== null
                    ? Json::encode((new EnumCollection($value))->toValues())
                    : null;

                return [$key => $value];
            }

            public function serialize($model, string $key, $value, array $attributes)
            {
                return (new EnumCollection($value))->toValues();
            }

        };
    }

    /**
     * Specify the Enum for the cast.
     *
     * @param class-string $class
     * @return string
     */
    public static function of($class)
    {
        return static::class . ':' . $class;
    }
}
