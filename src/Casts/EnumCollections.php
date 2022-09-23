<?php

namespace Datomatic\EnumCollection\Casts;

use BackedEnum;
use Datomatic\EnumCollection\EnumCollection;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class EnumCollections implements CastsAttributes
{
    /**
     * @param  Model  $model
     * @param  string  $key
     * @param  string  $value
     * @param  array  $attributes
     * @return EnumCollection|mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        $enumArray = json_decode($value);
        if (! is_array($enumArray)) {
            return new EnumCollection();
        }

        $enumClass = $this->getEnumCollectionClass($model, $key);

        $enumCollection = new EnumCollection();
        foreach ($enumArray as $enumValue) {
            $enum = null;

            if (is_subclass_of($enumClass, BackedEnum::class)) {
                $enum = $enumClass::tryFrom($enumValue);
            }

            if (! $enum && is_string($value)) {
                foreach ($enumClass::cases() as $case) {
                    if ($case->name === $value) {
                        $enum = $case;
                        break;
                    }
                }
            }

            if ($enum) {
                $enumCollection->push($enum);
            }
        }

        return $enumCollection;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  EnumCollection|array  $enumCollection
     * @param  array  $attributes
     * @return string|false
     */
    public function set($model, string $key, $enumCollection, array $attributes)
    {
        $enumClass = $this->getEnumCollectionClass($model, $key);

        return json_encode(collect($enumCollection)->map(function ($value) use ($enumClass) {
            if ($value instanceof UnitEnum) {
                if ($value instanceof BackedEnum) {
                    return $value->value;
                }

                return $value->name;
            }

            if (is_subclass_of($enumClass, BackedEnum::class)) {
                return $enumClass::tryfrom(intval($value)) ?? $enumClass::tryfrom($value);
            } else {
                return $enumClass::fromName(intval($value));
            }
        })->toArray());
    }

    /**
     * @param  Model  $model
     * @param  string  $key
     * @return string
     *
     * @throws \Exception
     */
    private function getEnumCollectionClass(Model $model, string $key): string
    {
        if (empty($model->enumCollections) || ! is_array($model->enumCollections)) {
            throw new \Exception('enumCollections array property not defined on Model '.get_class($model));
        }

        if (! isset($model->enumCollections[$key])) {
            throw new \Exception('On model '.get_class($model)." enumCollections array don't has '{$key}' key");
        }

        return $model->enumCollections[$key];
    }
}
