<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Casts;

use Datomatic\EnumCollections\EnumCollection;
use Datomatic\EnumCollections\Exceptions\MissingEnumClass;
use Datomatic\EnumCollections\Exceptions\WrongEnumClass;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
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
                $enumClass = $this->getClassEnum();

                if (! isset($attributes[$key])) {
                    return EnumCollection::of($enumClass);
                }

                $data = Json::decode($attributes[$key]);

                if (! is_array($data)) {
                    return EnumCollection::of($enumClass);
                }

                $enumClass = $this->getClassEnum();
                $unique = $this->getUnique();

                return EnumCollection::of($enumClass)->tryFrom($data)
                    ->when($unique, fn (EnumCollection $col) => $col->unique()->values());
            }

            /**
             * @param  Arrayable<int, int|string|TValue>|iterable<int, int|string|TValue>|int|string|null|EnumCollection  $value
             */
            public function set($model, $key, $value, $attributes)
            {
                $enumClass = $this->getClassEnum();
                $unique = $this->getUnique();

                if ($value instanceof EnumCollection) {
                    $values = $value;
                } else {
                    $values = EnumCollection::of($enumClass)->tryFrom($value);
                }

                $values = $values
                    ->when($unique, fn (EnumCollection $col) => $col->unique()->values())
                    ->toValues();

                $value = $value !== null ? Json::encode($values) : null;

                return [$key => $value];
            }

            /**
             * @param  Arrayable<int, int|string|TValue>|iterable<int, int|string|TValue>|int|string|null  $value
             * @param  array<int,string>  $attributes
             * @return array<TKey, int|string>
             */
            public function serialize(mixed $model, string $key, mixed $value, array $attributes): array
            {
                $enumClass = $this->getClassEnum();

                if ($value instanceof EnumCollection) {
                    $values = $value;
                } else {
                    $values = EnumCollection::of($enumClass)->tryFrom($value);
                }

                return $values
                    ->when($this->getUnique(), fn (EnumCollection $col) => $col->unique()->values())
                    ->toValues();
            }

            /**
             * @return class-string<TValue>
             */
            public function getClassEnum(): string
            {
                $enumClass = $this->arguments[0];

                if (! $enumClass) {
                    throw new MissingEnumClass('Missing enum class on AsLaravelEnumCollection cast definition');
                }

                if (! enum_exists($enumClass)) {
                    throw new WrongEnumClass('enumClass '.$enumClass.' does not exist');
                }

                /** @var class-string<TValue> $enumClass */
                return $enumClass;
            }

            public function getUnique(): bool
            {
                return isset($this->arguments[1]) ? filter_var($this->arguments[1], FILTER_VALIDATE_BOOLEAN) : false;
            }
        };
    }

    /**
     * Specify the enum-class for the cast.
     *
     * @param  class-string  $class
     * @return string
     */
    public static function of(string $class, bool $unique = false)
    {
        return static::class.':'.$class.','.($unique ? 'true' : 'false');
    }
}
