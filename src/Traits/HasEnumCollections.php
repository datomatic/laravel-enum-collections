<?php

namespace Datomatic\EnumCollections\Traits;

use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|self whereEnumCollectionContains(string $key, $value)
 * @method static \Illuminate\Database\Eloquent\Builder|self orWhereEnumCollectionContains(string $key, $value)
 * @method static \Illuminate\Database\Eloquent\Builder|self whereEnumCollectionDoesntContain(string $key, $value)
 * @method static \Illuminate\Database\Eloquent\Builder|self orWhereEnumCollectionDoesntContain(string $key, $value)
 */
trait HasEnumCollections
{
    public function scopeWhereEnumCollectionContains(Builder $query, string $key, $value): Builder
    {
        return $this->enumCollectionPrepare($query, $key, $value,
            fn (Builder $query, $value) => $query->whereJsonContains($key, $value)
        );
    }

    public function scopeOrWhereEnumCollectionContains(Builder $query, string $key, $value): Builder
    {
        return $this->enumCollectionPrepare($query, $key, $value,
            fn (Builder $query, $value) => $query->orWhereJsonContains($key, $value)
        );
    }

    public function scopeWhereEnumCollectionDoesntContain(Builder $query, string $key, $value): Builder
    {
        return $this->enumCollectionPrepare($query, $key, $value,
            fn (Builder $query, $value) => $query->whereJsonDoesntContain($key, $value)
        );
    }

    public function scopeOrWhereEnumCollectionDoesntContain(Builder $query, string $key, $value): Builder
    {
        return $this->enumCollectionPrepare($query, $key, $value,
            fn (Builder $query, $value) => $query->orWhereJsonDoesntContain($key, $value)
        );
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

    private function enumCollectionPrepare(Builder $query, string $key, mixed $value, callable $closure): Builder
    {
        if ($this->isEnumCollectionsAttribute($key)) {
            $value = $this->getValue($value, $key);

            return $closure($query, $value);
        }

        return $query;
    }

    private function getValue($value, string $key): mixed
    {
        if(is_array($value) || $value instanceof Collection){
            $values = [];
            foreach ($value as $v){
                $values[]= $this->getSingleValue($v, $key);
            }
            return $values;
        }
        return $this->getSingleValue($value, $key);
    }

    private function getSingleValue($value, string $key): mixed
    {
        $enumClass = $this->getEnumCollectionClass($key);

        if ($value instanceof UnitEnum) {
            $value = (is_subclass_of($enumClass, BackedEnum::class)) ? $value->value : $value->name;
        }

        return $value;
    }
}
