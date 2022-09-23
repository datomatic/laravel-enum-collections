<?php

namespace Datomatic\EnumCollections\Traits;

use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

trait HasEnumCollections
{
    public function scopeWhereEnumCollectionContains(Builder $query, string $key, $value): Builder
    {
        if ($this->isEnumCollectionsAttribute($key)) {
            $value = $this->getValue($value, $key);

            return $query->whereJsonContains($key, $value);
        }

        return $query;
    }

    public function scopeOrWhereEnumCollectionContains(Builder $query, string $key, $value): Builder
    {
        if ($this->isEnumCollectionsAttribute($key)) {
            $value = $this->getValue($value, $key);

            return $query->orWhereJsonContains($key, $value);
        }

        return $query;
    }

    public function isEnumCollectionsAttribute(string $key): bool
    {
        return in_array($key, $this->getEnumCollectionAttributes());
    }

    public function getEnumCollectionAttributes(): array
    {
        return is_array($this->enumCollections) ? array_keys($this->enumCollections) : [];
    }

    public function getEnumCollectionClass(string $key): ?string
    {
        return is_array($this->enumCollections)
            ? $this->enumCollections[$key] ?? null
            : null;
    }

    private function getValue($value, ?string $key = null, ?string $enumClass = null): mixed
    {
        if ($value instanceof UnitEnum) {
            $enumClass ??= $this->getEnumCollectionClass($key);
            $value = (is_subclass_of($enumClass, BackedEnum::class)) ? $value->value : $value->name;
        }

        return $value;
    }
}
