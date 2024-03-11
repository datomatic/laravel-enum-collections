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
 * @method static self from(Arrayable<int,int|string|UnitEnum|null>|iterable<UnitEnum|string|int|null>|null|UnitEnum|string|int $data, ?string $enumClass = null)
 * @method self tryFrom(Arrayable<int,int|string|UnitEnum|null>|iterable<UnitEnum|string|int|null>|null|UnitEnum|string|int $data)
 * @method static self tryFrom(Arrayable<int,int|string|UnitEnum|null>|iterable<UnitEnum|string|int|null>|null|UnitEnum|string|int $data, ?string $enumClass = null)
 * @method self from(Arrayable<int,int|string|UnitEnum|null>|iterable<UnitEnum|string|int|null>|null|UnitEnum|string|int $data)
 *
 * @extends Collection<int,UnitEnum>
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
     */
    public static function of(?string $enumClass): self
    {
        return (new self())->setEnumClass($enumClass);
    }

    public function contains($key, $operator = null, $value = null)
    {
        if (is_callable($key)) {
            return parent::contains($key, $operator, $value);
        }

        $firstEnum = $this->first();
        if ($firstEnum) {
            $this->enumClass ??= get_class($firstEnum);
            $enum = $this->tryGetEnumFromValue($key);

            return in_array($enum, $this->items);
        }

        return false;
    }

    /**
     * Specify the Enum for the cast.
     *
     * @param  ?class-string  $enumClass
     */
    public function setEnumClass(?string $enumClass): self
    {
        $this->enumClass = $enumClass;

        return $this;
    }

    public function getEnumClass(): ?string
    {
        return $this->enumClass;
    }

    public static function __callStatic($method, $parameters)
    {
        /** @var Arrayable<int,int|string|UnitEnum|null>|iterable<UnitEnum|string|int|null>|null|UnitEnum|string|int $data */
        $data = $parameters[0] ?? null;
        /** @var ?class-string $enumClass */
        $enumClass = $parameters[1] ?? null;

        if ($method === 'from') {
            return EnumCollection::of($enumClass)->from($data);
        }
        if ($method === 'tryFrom') {
            return EnumCollection::of($enumClass)->tryFrom($data);
        }
    }

    public function __call($method, $parameters)
    {
        /** @var Arrayable<int,int|string|UnitEnum|null>|iterable<int, int|string|UnitEnum|null>|null $data */
        $data = $parameters[0] ?? null;

        if ($method === 'tryFrom') {
            $this->items = collect($data)
                ->map(fn ($value) => $this->tryGetEnumFromValue($value)
                )->filter()->values()->all();

            return $this;
        }

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
    }

    /**
     * @throws Exception
     */
    public function tryGetEnumFromValue(UnitEnum|string|int $value): ?UnitEnum
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
        return $this->map(fn (UnitEnum|string|int $enum) => $this->getStorableEnumValue($enum))->toArray();
    }

    protected function getStorableEnumValue(UnitEnum|string|int $enum): int|string
    {
        if (is_string($enum) || is_int($enum)) {
            return $enum;
        }

        return $enum instanceof BackedEnum ? $enum->value : $enum->name;
    }
}
