<?php

namespace Datomatic\EnumCollections\Casts;

use BackedEnum;
use Datomatic\EnumCollections\EnumCollection;
use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use UnitEnum;

class EnumCollections implements CastsAttributes
{
    /**
     * @param  Model  $model
     * @param  string  $value
     * @return EnumCollection|mixed
     *
     * @throws Exception
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
            $enum = EnumCollection::tryGetEnumFromValue($enumValue, $enumClass);

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
     * @param  EnumCollection|array|string|int|UnitEnum|null  $enumCollection
     * @return string|false
     */
    public function set($model, string $key, $enumCollection, array $attributes)
    {
        $enumClass = $this->getEnumCollectionClass($model, $key);

        if (! $enumCollection instanceof Collection) {
            $enumCollection = collect(Arr::wrap($enumCollection));
        }

        return $enumCollection->map(function ($value) use ($enumClass) {
            $enum = EnumCollection::tryGetEnumFromValue($value, $enumClass);

            if ($enum) {
                if ($enum instanceof BackedEnum) {
                    return $enum->value;
                }

                return $enum->name;
            }

            return null;
        })->filter()->values()->toJson();
    }

    /**
     * @throws Exception
     */
    private function getEnumCollectionClass(Model $model, string $key): string
    {
        if (empty($model->enumCollections) || ! is_array($model->enumCollections)) {
            throw new Exception('enumCollections array property not defined on Model '.get_class($model));
        }

        if (! isset($model->enumCollections[$key])) {
            throw new Exception('On model '.get_class($model)." enumCollections array don't has '{$key}' key");
        }

        return $model->enumCollections[$key];
    }
}
