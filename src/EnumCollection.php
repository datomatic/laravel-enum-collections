<?php

namespace Datomatic\EnumCollections;

use BackedEnum;
use Datomatic\EnumCollections\Exceptions\ValueError;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use ReflectionEnum;
use UnitEnum;

/**
 * @template TKey of array-key
 * @template TValue of UnitEnum
 *
 * @method static self from(Arrayable<TKey, TValue>|iterable<TKey, TValue>|TValue|int|string|null $data, ?string $enumClass = null)
 * @method self from(Arrayable<TKey, int|string|TValue>|iterable<TKey, int|string|TValue>|TValue|int|string|null $data)
 * @method static self tryFrom(Arrayable<TKey, TValue>|iterable<TKey, TValue>|TValue|int|string|null $data, ?string $enumClass = null)
 * @method self tryFrom(Arrayable<TKey, int|string|TValue>|iterable<TKey, int|string|TValue>|TValue|int|string|null $data)
 *
 * @extends Collection<TKey,TValue>
 */
final class EnumCollection extends Collection
{
    /**
     *
     * @param  Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|null  $items
     * @param  class-string<TValue>|null  $enumClass
     */
    public function __construct(mixed $items = [], protected ?string $enumClass = null)
    {
        if ($items === []) {
            return;
        }

        $items = Arr::wrap($items);

        if (!$this->enumClass) {
            $item = array_values($items)[0] ?? null;
            if ($item instanceof UnitEnum) {
                $this->enumClass = get_class($item); //@phpstan-ignore-line
            }
        }

        throw_unless($this->enumClass,
            new Exceptions\MissingEnumClass('enumClass param is required when not pass an enum as argument'));

        foreach ($items as $key => $value) {
            $items[$key] = $this->tryGetEnumFromValue($value);

            if ($items[$key] === null) {
                throw new Exceptions\ValueError("Enum {$this->enumClass} does not contain {$value}");
            }
        }
        $this->items = $items;
    }

    /**
     * Specify the Enum for the cast.
     *
     * @param  ?class-string<TValue>  $enumClass
     * @return self<array-key,TValue>
     */
    public static function of(?string $enumClass): self
    {
        return new self(items: [], enumClass: $enumClass);
    }

    /**
     * Specify the Enum class for the cast.
     *
     * @param  ?class-string<TValue>  $enumClass
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
     * @param  string  $method
     * @param  array<int, Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|class-string|null>  $parameters
     *
     * @return EnumCollection|mixed
     */
    public static function __callStatic($method, $parameters)
    {
        /** @var Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|null $data */
        $data = $parameters[0] ?? null;
        /** @var ?class-string<TValue> $enumClass */
        $enumClass = $parameters[1] ?? null;

        if ($method === 'from') {
            return new EnumCollection($data, $enumClass);
        }
        if ($method === 'tryFrom') {
            if ($enumClass) {
                return EnumCollection::of($enumClass)->tryFrom($data);
            } else {
                try {
                    return (new EnumCollection(collect(Arr::wrap($data))->first()))->tryFrom($data);
                } catch (ValueError $e) {
                    return new self();
                }
            }
        }
        return parent::__callStatic($method, $parameters);
    }

    /**
     * @param  string  $method
     * @param  array<int, Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|class-string|null>  $parameters
     *
     * @return EnumCollection|mixed
     */
    public function __call($method, $parameters)
    {
        /** @var Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|null $data */
        $data = $parameters[0] ?? null;

        if ($method === 'from') {
            $this->items = [];
            foreach (Arr::wrap($data) as $key => $value) {
                $enum = $this->tryGetEnumFromValue($value);

                if ($enum === null) {
                    throw new Exceptions\ValueError("Enum {$this->enumClass} does not contain {$value}");
                }

                $this->items[$key] = $enum;
            }

            return $this;
        }

        if ($method === 'tryFrom') {
            $this->items = [];
            foreach (Arr::wrap($data) as $key => $value) {
                $enum = $this->tryGetEnumFromValue($value);

                if ($enum) {
                    $this->items[$key] = $enum;
                }
            }
            return $this;
        }

        return parent::__call($method, $parameters);
    }

    /**
     * @param  TValue|int|string|null  $value
     *
     * @return  TValue|null
     *
     * @throws Exception
     */
    public function tryGetEnumFromValue(UnitEnum|int|string|null $value): ?UnitEnum
    {
        if ($value instanceof UnitEnum || $value === null) {
            return $value;
        }

        throw_unless($this->enumClass,
            new Exceptions\MissingEnumClass('enumClass param is required when not pass an enum as argument'));

        if (is_string($value) && method_exists($this->enumClass, 'cases')) {
            foreach ($this->enumClass::cases() as $case) {
                if ($case->name === $value) {
                    return $case;
                }
            }
        }

        if (defined($this->enumClass.'::'.$value)) {
            $enum = constant($this->enumClass.'::'.$value);
            if ($enum instanceof UnitEnum) {
                return $enum; //@phpstan-ignore-line
            }
        }

        if (is_subclass_of($this->enumClass, BackedEnum::class)) {
            if ((new ReflectionEnum($this->enumClass))->getBackingType()?->getName() === 'int') {
                $value = intval($value);
            } else {
                $value = strval($value);
            }
            return $this->enumClass::tryFrom($value);
        }


        return null;
    }

    /**
     * @return array<TKey, int|string>
     */
    public function toValues(): array
    {
        return Arr::map($this->items, function (UnitEnum $enum) {
            /** @var TValue $enum */
            return $this->getStorableEnumValue($enum);
        }
        );
    }

    /**
     * @param  TValue  $enum
     */
    protected function getStorableEnumValue(UnitEnum $enum): int|string
    {
        return $enum instanceof BackedEnum ? $enum->value : $enum->name;
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|null  $items
     *
     * @return array<TKey, TValue>
     */
    protected function getArrayableItems($items): array
    {
        /** @var array<TKey, TValue> $array */
        $array = (new EnumCollection(items: $items, enumClass: $this->enumClass))->toArray();

        return $array;
    }

    /**
     * Results array of items from Collection or Arrayable.
     *
     * @param  Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|null  $items
     * @return array<TKey, int|string>
     */
    protected function getArrayableItemsValues($items): array
    {
        return (new EnumCollection(items: $items, enumClass: $this->enumClass))->toValues();
    }

    /**
     * @throws Exception
     */
    public function contains($key, $operator = null, $value = null): bool
    {
        if (!$key instanceof UnitEnum && is_callable($key)) {
            return parent::contains($key);
        }

        $firstEnum = $this->first();
        if ($firstEnum && is_object($firstEnum)) {
            $this->enumClass ??= get_class($firstEnum);
            $enum = $this->tryGetEnumFromValue($key); //@phpstan-ignore-line

            if ($enum === null) {
                return false;
            }

            return in_array($enum, $this->items);
        }

        return false;
    }

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param  callable(TValue, TKey): TMapValue  $callback
     * @return Collection<TKey, TMapValue>
     */
    public function map(callable $callback): Collection
    {
        return new parent(items: Arr::map($this->items, $callback));
    }

    /**
     * Run a map over each of the items.
     *
     * @param  callable(TValue, TKey): TValue  $callback
     * @return self<TKey, TValue>
     */
    public function enumsMap(callable $callback): self
    {
        return new self(items: Arr::map($this->items, $callback), enumClass: $this->enumClass);
    }

    /**
     * Get the items in the collection that are not present in the given items.
     *
     * @param  Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|null  $items
     *
     * @return static
     */
    public function diff($items)
    {
        return new static(items: array_diff($this->toValues(), $this->getArrayableItemsValues($items)),
            enumClass: $this->enumClass);
    }

    /**
     * Get the items in the collection that are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue>|TValue|int|string|null  $items
     * @param  callable(TValue, TValue): int  $callback
     * @return static
     */
    public function diffUsing($items, callable $callback)
    {
        // @phpstan-ignore-next-line
        return new static(items: array_udiff($this->items, $this->getArrayableItems($items), $callback),
            enumClass: $this->enumClass);
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function diffAssoc($items)
    {
        // @phpstan-ignore-next-line
        return new static(items: array_diff_assoc($this->toValues(), $this->getArrayableItemsValues($items)),
            enumClass: $this->enumClass);
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @param  callable(TKey, TKey): int  $callback
     * @return static
     */
    public function diffAssocUsing($items, callable $callback)
    {
        // @phpstan-ignore-next-line
        return new static(items: array_diff_uassoc($this->toValues(), $this->getArrayableItemsValues($items),
            $callback), enumClass: $this->enumClass); //@phpstan-ignore-line
    }

    /**
     * Get the items in the collection whose keys are not present in the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function diffKeys($items)
    {
        // @phpstan-ignore-next-line
        return new static(items: array_diff_key($this->items, $this->getArrayableItems($items)),
            enumClass: $this->enumClass);
    }

    /**
     * Get the items in the collection whose keys are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @param  callable(TKey, TKey): int  $callback
     * @return static
     */
    public function diffKeysUsing($items, callable $callback)
    {
        // @phpstan-ignore-next-line
        return new static(items: array_diff_ukey($this->items, $this->getArrayableItems($items), $callback),
            enumClass: $this->enumClass);
    }
}
