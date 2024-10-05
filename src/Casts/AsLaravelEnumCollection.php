<?php

namespace Datomatic\EnumCollections\Casts;

use Datomatic\EnumCollections\EnumCollection;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Casts\Json;
use UnitEnum;

/**
 * @template TValue of UnitEnum
 * @template TKey of array-key
 */
class AsLaravelEnumCollection implements Castable
{
    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     *
     * @param  array{class-string<TValue>}  $arguments
     * @return \Illuminate\Contracts\Database\Eloquent\CastsAttributes<\Illuminate\Support\Collection<array-key, TValue>, iterable<TValue>>
     */
    public static function castUsing(array $arguments)
    {
        return new class($arguments) implements CastsAttributes
        {
            /**
             * @var array<int, string>
             */
            protected array $arguments;

            /**
             * @param  array<int, string>  $arguments
             */
            public function __construct(array $arguments)
            {
                $this->arguments = $arguments;
            }

            public function get($model, $key, $value, $attributes)
            {
                if (! isset($attributes[$key])) {
                    return;
                }

                $data = Json::decode($attributes[$key]);

                if (! is_array($data)) {
                    return;
                }

                /** @var class-string<TValue>|null $enumClass */
                $enumClass = $this->arguments[0];

                return EnumCollection::of($enumClass)->tryFrom($data);
            }

            /**
             * @param  \Illuminate\Contracts\Support\Arrayable<int, int|string|TValue>|iterable<int, int|string|TValue>|int|string|null  $value
             */
            public function set($model, $key, $value, $attributes)
            {
                /** @var class-string<TValue>|null $enumClass */
                $enumClass = $this->arguments[0];

                $value = $value !== null
                    ? Json::encode(EnumCollection::of($enumClass)->tryFrom($value)->toValues())
                    : null;

                return [$key => $value];
            }

            /**
             * @param  \Illuminate\Contracts\Support\Arrayable<int, int|string|TValue>|iterable<int, int|string|TValue>|int|string|null  $value
             * @param  array<int,string>  $attributes
             * @return array<TKey, int|string>
             */
            public function serialize(mixed $model, string $key, mixed $value, array $attributes): array
            {

                /** @var class-string<TValue>|null $enumClass */
                $enumClass = $this->arguments[0];

                return (new EnumCollection($value, $enumClass))->toValues();
            }
        };
    }
}
