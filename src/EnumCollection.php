<?php

namespace Datomatic\EnumCollections;

use BackedEnum;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use ReflectionEnum;
use UnitEnum;
use ValueError;

/**
 * @template TKey of array-key
 * @template TValue of UnitEnum|int|string
 *
 * @method static self from(Arrayable<TKey, TValue>|iterable<TKey, TValue>|TValue|null $data, ?string $enumClass = null)
 * @method self from(Arrayable<TKey, TValue>|iterable<TKey, TValue>|TValue|null $data)
 * @method static self tryFrom(Arrayable<TKey, TValue>|iterable<TKey, TValue>|TValue|null $data, ?string $enumClass = null)
 * @method self tryFrom(Arrayable<TKey, TValue>|iterable<TKey, TValue>|TValue|null $data)
 *
 * @extends Collection<TKey,TValue>
 */
class EnumCollection extends Collection
{
    /**
     * @var class-string|null
     */
    protected ?string $enumClass;

    /**
     * Specify the Enum for the cast.
     *
     * @param  ?class-string  $enumClass
     * @return self<array-key,UnitEnum|int|string>
     */
    public static function of(?string $enumClass): self
    {
        return (new self)->setEnumClass($enumClass);
    }

    /**
     * @throws Exception
     */
    public function contains($key, $operator = null, $value = null): bool
    {
        if (! $key instanceof UnitEnum && is_callable($key)) {
            return parent::contains($key, $operator, $value);
        }

        $firstEnum = $this->first();
        if ($firstEnum && is_object($firstEnum)) {
            $this->enumClass ??= get_class($firstEnum);
            if ($key instanceof UnitEnum || is_string($key) || is_int($key)) {
                $enum = $this->tryGetEnumFromValue($key);

                return in_array($enum, $this->items);
            }

        }

        return false;
    }

    /**
     * Specify the Enum class for the cast.
     *
     * @param  ?class-string  $enumClass
     * @return self<TKey,TValue>
     */
    public function setEnumClass(?string $enumClass): self
    {
        $this->enumClass = $enumClass;

        return $this;
    }

    /**
     * Get the Enum class.
     *
     * @return ?class-string
     */
    public function getEnumClass(): ?string
    {
        return $this->enumClass;
    }

    /**
     * @return EnumCollection|mixed
     */
    public static function __callStatic($method, $parameters)
    {
        /** @var Arrayable<TKey,TValue>|iterable<TKey,TValue>|TValue $data */
        $data = $parameters[0] ?? null;
        /** @var ?class-string $enumClass */
        $enumClass = $parameters[1] ?? null;

        if ($method === 'from') {
            return EnumCollection::of($enumClass)->from($data);
        }
        if ($method === 'tryFrom') {
            return EnumCollection::of($enumClass)->tryFrom($data);
        }

        return parent::__callStatic($method, $parameters);
    }

    /**
     * @return EnumCollection|mixed
     */
    public function __call($method, $parameters)
    {
        /** @var Arrayable<TKey,TValue>|iterable<TKey, TValue>|null $data */
        $data = $parameters[0] ?? null;

        if ($method === 'from') {
            $this->items = collect($data)
                ->map(function ($value) {
                    $enum = $this->tryGetEnumFromValue($value);

                    if ($enum === null) {
                        throw new ValueError("Enum {$this->enumClass} does not contain {$value}");
                    }

                    return $enum;
                })->all();

            return $this;
        }

        if ($method === 'tryFrom') {
            $this->items = collect($data)
                ->map(fn ($value) => $this->tryGetEnumFromValue($value)
                )->filter()->values()->all();

            return $this;
        }

        return parent::__call($method, $parameters);
    }

    /**
     * @param  TValue  $value
     * @return ?UnitEnum
     *
     * @throws Exception
     */
    public function tryGetEnumFromValue(UnitEnum|int|string $value): ?UnitEnum
    {
        if ($value instanceof UnitEnum) {
            return $value;
        }
        throw_unless($this->enumClass, new Exception('enumClass param is required when not pass an enum as argument'));

        if (is_string($value) && method_exists($this->enumClass, 'cases')) {
            foreach ($this->enumClass::cases() as $case) {
                if ($case->name === $value) {
                    return $case;
                }
            }
        }

        if (is_subclass_of($this->enumClass, BackedEnum::class)) {
            if ((new ReflectionEnum($this->enumClass))->getBackingType()?->getName() === 'int') {

                return $this->enumClass::tryFrom((int) $value);
            }

            return $this->enumClass::tryFrom((string) $value);
        }

        if (defined($this->enumClass.'::'.$value)) {
            $enum = constant($this->enumClass.'::'.$value);
            if ($enum instanceof UnitEnum) {
                return $enum;
            }
        }

        return null;
    }

    public function toValues(): array
    {
        return $this->map(function (UnitEnum|int|string $enum) {
            /**
             * @param  TValue  $enum
             */
            return $this->getStorableEnumValue($enum);
        })->toArray();
    }

    /**
     * @param  TValue  $enum
     */
    protected function getStorableEnumValue(UnitEnum|int|string $enum): int|string
    {
        if (is_string($enum) || is_int($enum)) {
            return $enum;
        }

        return $enum instanceof BackedEnum ? $enum->value : $enum->name;
    }
}
