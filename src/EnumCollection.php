<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections;

use BackedEnum;
use Datomatic\EnumCollections\Exceptions\MethodNotSupported;
use Datomatic\EnumCollections\Exceptions\ValueError;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
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
    protected EnumType $enumType;

    /** @var class-string<TValue> */
    protected string $enumClass;

    /**
     * @param  Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|null  $items
     * @param  class-string<TValue>|null  $enumClass
     */
    public function __construct(mixed $items = [], ?string $enumClass = null)
    {
        if ($items instanceof Arrayable) {
            if ($items instanceof EnumCollection) {
                /** @var class-string<TValue> $enumClass */
                $enumClass = $items->getEnumClass();
            }

            $items = $items->toArray();
        }

        if ($enumClass) {
            $this->setEnumClass($enumClass);
        }

        if ($items === []) {
            return;
        }

        $items = $this->privateFlatten(Arr::wrap($items));

        if (! $enumClass) {
            $item = array_values($items)[0] ?? null;
            if ($item instanceof UnitEnum) {
                $this->setEnumClass(get_class($item));
            }
        }

        throw_unless(
            isset($this->enumClass),
            new Exceptions\MissingEnumClass('enumClass param is required when not pass an enum as argument')
        );

        foreach ($items as $key => $value) {
            $enum = $this->tryGetEnumFromValue($value);

            if ($enum === null) {
                /** @var int|string|null $value */
                throw new Exceptions\ValueError("Enum {$this->enumClass} does not contain {$value}");
            }
            $items[$key] = $enum;
        }
        /** @var array<TKey,TValue> $items */
        $this->items = $items;
    }

    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @template TMakeKey of array-key
     * @template TMakeValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TMakeKey, TMakeValue>|iterable<TMakeKey, TMakeValue>|null  $items
     * @param  class-string<TValue>|null  $enumClass
     * @return static<TMakeKey, TMakeValue>
     */
    public static function make($items = [], ?string $enumClass = null)
    {
        // TODO: from my pov, this should just throw if no enum class is provided
        return new self($items, $enumClass);
    }

    /**
     * Set the item at a given offset.
     *
     * @param  TKey|null  $key
     * @param  TValue  $value
     */
    public function offsetSet($key, $value): void
    {
        $val = $this->tryGetEnumFromValue($value);
        if (is_null($val)) {
            throw new ValueError($value.' can\'t be converted into an instance of '.$this->enumClass);
        }
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Specify the Enum for the cast.
     *
     * @param  ?class-string<TValue>  $enumClass
     * @return self<array-key,TValue>
     */
    public static function of(?string $enumClass): self
    {
        throw_unless(
            $enumClass,
            new Exceptions\MissingEnumClass('enumClass param is required')
        );

        return new self(items: [], enumClass: $enumClass);
    }

    /**
     * Specify the Enum class for the cast.
     *
     * @param  ?class-string<TValue>  $enumClass
     */
    protected function setEnumClass(?string $enumClass): void
    {
        if (! $enumClass) {
            throw new Exceptions\MissingEnumClass('enumClass param is required when not pass an enum as argument');
        }

        if (! enum_exists($enumClass)) {
            throw new Exceptions\WrongEnumClass('enumClass '.$enumClass.' does not exist');
        }

        $this->enumClass = $enumClass;

        $reflection = new ReflectionEnum($enumClass);
        if ($reflectionType = $reflection->getBackingType()) {
            if ($reflectionType->getName() === 'int') {
                $this->enumType = EnumType::INT_BACKED_ENUM;
            } else {
                $this->enumType = EnumType::STRING_BACKED_ENUM;
            }
        } else {
            $this->enumType = EnumType::UNIT_ENUM;
        }
    }

    /**
     * Get the Enum class.
     *
     * @return class-string<TValue>
     */
    public function getEnumClass(): string
    {
        return $this->enumClass;
    }

    /**
     * @param  array<TKey,TValue|int|string|null|array<TKey,TValue|int|string|null>>  $array
     * @return array<TKey,TValue|int|string|null>
     */
    private function privateFlatten(array $array): array
    {
        $return = [];
        array_walk_recursive($array, function (mixed $a, int|string $key) use (&$return) {
            if (! isset($return[$key])) {
                $return[$key] = $a;
            } else {
                $return[] = $a;
            }
        });

        /** @var array<TKey,TValue|int|string|null> $return */
        return $return;
    }

    /**
     * @param  string  $method
     * @param  array<int, Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|class-string|null>  $parameters
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
                    return new self;
                }
            }
        }

        return parent::__callStatic($method, $parameters);
    }

    /**
     * @param  string  $method
     * @param  array<int, Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|class-string|null>  $parameters
     * @return EnumCollection|mixed
     */
    public function __call($method, $parameters)
    {
        /** @var Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|null $data */
        $data = $parameters[0] ?? null;
        $data = $this->privateFlatten(Arr::wrap($data));

        if ($method === 'from') {
            $this->items = [];
            foreach ($data as $key => $value) {
                $enum = $this->tryGetEnumFromValue($value);

                if ($enum === null) {
                    /** @var int|string|null $value */
                    throw new Exceptions\ValueError("Enum {$this->enumClass} does not contain {$value}");
                }

                $this->items[$key] = $enum;
            }

            return $this;
        }

        if ($method === 'tryFrom') {
            $this->items = [];
            foreach ($data as $key => $value) {
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
     * @return TValue|null
     *
     * @throws Exception
     */
    public function tryGetEnumFromValue(UnitEnum|int|string|null $value): ?UnitEnum
    {
        if ($value instanceof UnitEnum || $value === null) {
            return $value;
        }

        if (is_string($value)) {
            foreach ($this->enumClass::cases() as $case) {
                if ($case->name === $value) {
                    return $case;
                }
            }
        }

        if ($this->enumType !== EnumType::UNIT_ENUM) {
            $value = match ($this->enumType) {
                EnumType::INT_BACKED_ENUM => intval($value),
                EnumType::STRING_BACKED_ENUM => strval($value),
            };

            return $this->enumClass::tryFrom($value); // @phpstan-ignore-line
        }

        return null;
    }

    /**
     * @return array<TKey, int|string>
     */
    public function toValues(): array
    {
        return Arr::map(
            $this->items,
            function (UnitEnum $enum) {
                /** @var TValue $enum */
                return $this->getStorableEnumValue($enum);
            }
        );
    }
    /**
     * @return array<TKey, int|string>
     */
    public function toCollectionValues(): Collection
    {
        return new parent($this->toValues());
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
     * @return array<TKey, TValue>
     */
    protected function getArrayableItems($items): array
    {
        /** @var array<TKey, TValue> $array */
        $array = (new EnumCollection(items: $items, enumClass: $this->enumClass))->toArray();

        return $array;
    }

    protected function getCollectionArrayableItems($items): array
    {
        return parent::getArrayableItems($items);
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
     * @param  TValue|int|string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     *
     * @throws Exception
     */
    public function contains($key, $operator = null, $value = null): bool
    {
        if (! $key instanceof UnitEnum && is_callable($key)) {
            return parent::contains($key);
        }

        $enum = $this->tryGetEnumFromValue($key);

        if ($enum === null) {
            return false;
        }

        return in_array($enum, $this->items);
    }

    /**
     * @param  TValue|int|string|array<TValue|int|string>  $values
     *
     * @throws Exception
     */
    public function containsAny(mixed $values): bool
    {
        /** @var array<int,TValue> $values */
        $values = array_values(Arr::whereNotNull(
            Arr::map(
                Arr::wrap($values),
                fn ($value) => $this->tryGetEnumFromValue($value)
            )
        ));

        foreach ($this->items as $enum) {
            if (in_array($enum, $values)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  TValue|int|string|array<TValue|int|string>  $values
     *
     * @throws Exception
     */
    public function doesntContainAny(mixed $values): bool
    {
        return ! $this->containsAny($values);
    }

    /**
     * @param  TValue  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     *
     * @throws Exception
     */
    public function containsStrict($key, $operator = null, $value = null): bool
    {
        if (! $key instanceof UnitEnum) {
            throw new Exceptions\ValueError('Value must be an instance of UnitEnum');
        }

        return $this->contains($key, $operator, $value);
    }

    /**
     * Run a map over each of the items.
     *
     * @template TMapValue
     *
     * @param  callable(TValue, TKey): TMapValue  $callback
     * @return parent<TKey, TMapValue>
     */
    public function map(callable $callback): Collection
    {
        return new parent(items: Arr::map($this->items, $callback));
    }

    /**
     * Run a map over each of the items.
     *
     * @param  callable(TValue, TKey): TValue|int|string  $callback
     * @return self<TKey, TValue>
     */
    public function mapStrict(callable $callback): self
    {
        return new self(items: Arr::map($this->items, $callback), enumClass: $this->enumClass);
    }

    /**
     * Get the items in the collection that are not present in the given items.
     *
     * @param  Arrayable<TKey,TValue|int|string>|iterable<TKey,TValue|int|string>|TValue|int|string|null  $items
     * @return static
     */
    public function diff($items)
    {
        return new self(
            items: array_diff($this->toValues(), $this->getArrayableItemsValues($items)),
            enumClass: $this->enumClass
        );
    }

    /**
     * Get the items in the collection that are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue|int|string>|iterable<TKey, TValue|int|string>|TValue|int|string|null  $items
     * @param  callable(TValue, TValue): int  $callback
     * @return static
     */
    public function diffUsing($items, callable $callback)
    {
        /** @var callable(mixed, mixed): int $callback */
        return new self(
            items: array_udiff($this->items, $this->getArrayableItems($items), $callback),
            enumClass: $this->enumClass
        );
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue|int|string>|iterable<TKey, TValue|int|string>|TValue|int|string|null  $items
     * @return static
     */
    public function diffAssoc($items)
    {
        return new self(
            items: array_diff_assoc($this->toValues(), $this->getArrayableItemsValues($items)),
            enumClass: $this->enumClass
        );
    }

    /**
     * Get the items in the collection whose keys and values are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue|int|string>|iterable<TKey, TValue|int|string>|TValue|int|string|null  $items
     * @param  callable(TKey, TKey): int  $callback
     * @return static
     */
    public function diffAssocUsing($items, callable $callback)
    {
        /** @var callable(mixed, mixed): int $callback */
        return new self(items: array_diff_uassoc(
            $this->toValues(),
            $this->getArrayableItemsValues($items),
            $callback
        ), enumClass: $this->enumClass);
    }

    /**
     * Get the items in the collection whose keys are not present in the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue|int|string>|iterable<TKey, TValue|int|string>|TValue|int|string|null  $items
     * @return static
     */
    public function diffKeys($items)
    {
        return new self(
            items: array_diff_key($this->items, $this->getArrayableItems($items)),
            enumClass: $this->enumClass
        );
    }

    /**
     * Get the items in the collection whose keys are not present in the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue|int|string>|iterable<TKey, TValue|int|string>|TValue|int|string|null  $items
     * @param  callable(TKey, TKey): int  $callback
     * @return static
     */
    public function diffKeysUsing($items, callable $callback)
    {
        /** @var callable(mixed, mixed): int $callback */
        return new self(
            items: array_diff_ukey($this->items, $this->getArrayableItems($items), $callback),
            enumClass: $this->enumClass
        );
    }

    /**
     * Get all items except for those with the specified keys.
     *
     * @param  \Illuminate\Support\Enumerable<array-key, TKey>|array<array-key, TKey>|string  $keys
     * @return static
     */
    public function except(mixed $keys)
    {
        if ($keys instanceof Enumerable) {
            $keys = $keys->all();
        } elseif (! is_array($keys)) {
            $keys = func_get_args();
        }

        /** @var array<int,int|string> $keys */
        return new self(items: Arr::except($this->items, $keys), enumClass: $this->enumClass);
    }

    /**
     * Run a filter over each of the items.
     *
     * @param  (callable(TValue, TKey): bool)|null  $callback
     * @return static
     */
    public function filter(?callable $callback = null)
    {
        if ($callback) {
            return new self(items: Arr::where($this->items, $callback), enumClass: $this->enumClass);
        }

        return new self(items: array_filter($this->items), enumClass: $this->enumClass);
    }

    /**
     * Get a flattened array of the items in the collection.
     *
     * @param  int  $depth
     * @return static<int, TValue>
     */
    public function flatten($depth = 1)
    {
        return new self(items: Arr::flatten($this->items, $depth), enumClass: $this->enumClass);
    }

    /**
     * Remove an item from the collection by key.
     *
     * \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TKey>|TKey  $keys
     *
     * @return $this
     */
    public function forget($keys)
    {
        foreach ($this->getCollectionArrayableItems($keys) as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    /**
     * Intersect the collection with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function intersect($items)
    {
        return new self(items: array_intersect($this->toValues(), $this->getArrayableItemsValues($items)), enumClass: $this->enumClass);
    }

    /**
     * Intersect the collection with the given items, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue|int|string>|iterable<TKey, TValue|int|string>|TValue|int|string|null  $items
     * @param  callable(TValue, TValue): int  $callback
     * @return self
     */
    public function intersectUsing($items, callable $callback)
    {
        return new self(array_uintersect($this->items, $this->getArrayableItems($items), $callback), enumClass: $this->enumClass);
    }

    /**
     * Intersect the collection with the given items with additional index check.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function intersectAssoc($items)
    {
        return new self(
            items: array_intersect_assoc($this->toValues(), $this->getArrayableItemsValues($items)),
            enumClass: $this->enumClass
        );
    }

    /**
     * Intersect the collection with the given items with additional index check, using the callback.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<array-key, TValue>|iterable<array-key, TValue>  $items
     * @param  callable(TValue, TValue): int  $callback
     * @return static
     */
    public function intersectAssocUsing($items, callable $callback)
    {
        /** @var callable(mixed, mixed): int $callback */
        return new self(items: array_intersect_uassoc(
            $this->toValues(),
            $this->getArrayableItemsValues($items),
            $callback
        ), enumClass: $this->enumClass);
    }

    /**
     * Intersect the collection with the given items by key.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue|int|string>|iterable<TKey, TValue|int|string>|TValue|int|string|null  $items
     * @return static
     */
    public function intersectByKeys($items)
    {
        return new self(items: array_intersect_key(
            $this->toValues(), $this->getArrayableItems($items)
        ), enumClass: $this->enumClass);
    }


    /**
     * Join all items from the collection using a string. The final items can use a separate glue string.
     *
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return string
     */
    public function join($glue, $finalGlue = '')
    {
        return $this->toCollectionValues()->join($glue, $finalGlue);
    }


    /**
     * @param mixed $value
     * @param mixed $key
     * @return mixed
     * @throws MethodNotSupported
     */
    public function pluck($value, $key = null)
    {
        throw new MethodNotSupported('pluck');
    }

    /**
     * Run a dictionary map over the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TMapToDictionaryKey of array-key
     * @template TMapToDictionaryValue
     *
     * @param  callable(TValue, TKey): array<TMapToDictionaryKey, TMapToDictionaryValue>  $callback
     * @return static<TMapToDictionaryKey, array<int, TMapToDictionaryValue>>
     */
    public function mapToDictionary(callable $callback): Collection
    {
        return $this->toBase()->mapToDictionary($callback);
    }

    /**
     * Run an associative map over each of the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TMapWithKeysKey of array-key
     * @template TMapWithKeysValue
     *
     * @param  callable(TValue, TKey): array<TMapWithKeysKey, TMapWithKeysValue>  $callback
     * @return parent<TMapWithKeysKey, TMapWithKeysValue>
     */
    public function mapWithKeys(callable $callback)
    {
        return new parent(Arr::mapWithKeys($this->items, $callback));
    }

    /**
     * Run an associative map over each of the items.
     *
     * The callback should return an associative array with a single key/value pair.
     *
     * @template TMapWithKeysKey of array-key
     *
     * @param  callable(TValue, TKey): array<TMapWithKeysKey, TValue|int|string>  $callback
     * @return static<TMapWithKeysKey, TValue>
     */
    public function mapWithKeysStrict(callable $callback)
    {
        return new self(Arr::mapWithKeys($this->items, $callback), enumClass: $this->enumClass);
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>  $items
     * @return static
     */
    public function merge($items)
    {
        return new self(array_merge($this->toValues(), $this->getArrayableItemsValues($items)), enumClass: $this->enumClass);
    }

    public function mergeRecursive($items)
    {
        throw new MethodNotSupported('mergeRecursive');
    }


    /**
     * Multiply the items in the collection by the multiplier.
     *
     * @param  int  $multiplier
     * @return static
     */
    public function multiply(int $multiplier)
    {
        $new = new static([],$this->enumClass);

        for ($i = 0; $i < $multiplier; $i++) {
            $new->push(...$this->items);
        }

        return $new;
    }

    /**
     * Create a collection by using this collection for keys and another for its values.
     *
     * @template TCombineValue
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue|int|string>|iterable<TKey, TValue|int|string>|TValue|int|string|null  $items
     * @return parent<TValue, TCombineValue>
     */
    public function combine($values)
    {
        return new parent(array_combine($this->toValues(), parent::getArrayableItems($values)));
    }

    /**
     * Union the collection with the given items.
     *
     * @param  \Illuminate\Contracts\Support\Arrayable<TKey, TValue|int|string>|iterable<TKey, TValue|int|string>|TValue|int|string|null  $items
     * @return static
     */
    public function union($items)
    {
        return new static($this->items + $this->getArrayableItems($items), enumClass: $this->enumClass);
    }

    /**
     * Concatenate values of a given key as a string.
     *
     * @param  (callable(TValue, TKey): mixed)|string|null  $value
     * @param  string|null  $glue
     * @return string
     */
    public function implode($value, $glue = null)
    {
        return $this->toCollectionValues()->implode($value, $glue);
    }

    /**
     * Get the keys of the collection items.
     *
     * @return static<int, TKey>
     */
    public function keys(): Collection
    {
        return new parent(array_keys($this->items));
    }

    // public function select($keys) {}

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param  TValue  $value
     * @param  TKey  $key
     * @return $this
     */
    public function prepend($value, $key = null)
    {
        return new self(Arr::prepend($this->items, ...func_get_args()), $this->enumClass);
    }


    /**
     * @return mixed
     * @throws MethodNotSupported
     */
    public static function empty()
    {
        throw new MethodNotSupported('empty');
    }

}
