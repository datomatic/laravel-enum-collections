<?php

namespace Datomatic\EnumCollections;

use BackedEnum;
use Exception;
use Illuminate\Support\Collection;
use ReflectionEnum;
use UnitEnum;
use ValueError;

/**
 * @method static self from(iterable $data, ?string $enumClass = null)
 * @method static self tryFrom(iterable $data, ?string $enumClass = null)
 * @method self from(iterable $data)
 * @method self tryFrom(iterable $data)
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
     * @param  ?class-string  $class
     */
    public static function of(?string $enumClass): self
    {
        return (new self())->setEnumClass($enumClass);
    }

    public function contains($key, $operator = null, $value = null)
    {
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
     * @param  ?class-string  $class
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
        if ($method === 'from') {
            return EnumCollection::of($parameters[1] ?? null)->from($parameters[0]);
        }
        if ($method === 'tryFrom') {
            return EnumCollection::of($parameters[1] ?? null)->tryFrom($parameters[0]);
        }
    }

    public function __call($method, $parameters)
    {
        if ($method === 'tryFrom') {
            $this->items = collect($parameters[0])
                ->map(fn ($value) => $this->tryGetEnumFromValue($value)
                )->filter()->values()->all();

            return $this;
        }

        if ($method === 'from') {
            $this->items = collect($parameters[0])
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
    public function tryGetEnumFromValue(mixed $value): ?UnitEnum
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
            if ((new ReflectionEnum($this->enumClass))->getBackingType()->getName() === 'int') {
                return $this->enumClass::tryFrom((int) $value);
            }

            return $this->enumClass::tryFrom((string) $value);
        }

        if (defined($this->enumClass.'::'.$value)) {
            return constant($this->enumClass.'::'.$value);
        }

        return null;
    }

    public function toValues(): array
    {
        return $this->map(fn ($enum) => $this->getStorableEnumValue($enum))->toArray();
    }

    protected function getStorableEnumValue($enum)
    {
        if (is_string($enum) || is_int($enum)) {
            return $enum;
        }

        return $enum instanceof BackedEnum ? $enum->value : $enum->name;
    }
}
