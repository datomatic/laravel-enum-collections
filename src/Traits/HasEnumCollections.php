<?php

namespace Datomatic\EnumCollections\Traits;

use BackedEnum;
use Datomatic\EnumCollections\Casts\AsEnumCollection;
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

    public function hasEnumCollectionCast(string $key): bool
    {
        $casts = $this->getCasts();

        if (isset($casts[$key]) && str($casts[$key])->contains(AsEnumCollection::class)) {
            return true;
        }

        return false;
    }

    private function enumCollectionPrepare(Builder $query, string $key, mixed $value, callable $closure): Builder
    {
        if ($this->hasEnumCollectionCast($key)) {
            return $closure($query, $this->getValue($value));
        }

        return $query;
    }

    private function getValue($value): mixed
    {
        if (is_array($value) || $value instanceof Collection) {
            $values = [];
            foreach ($value as $v) {
                $values[] = $this->getSingleValue($v);
            }

            return $values;
        }

        return $this->getSingleValue($value);
    }

    private function getSingleValue($value): mixed
    {
        if ($value instanceof UnitEnum) {
            $value = ($value instanceof BackedEnum) ? $value->value : $value->name;
        }

        return $value;
    }
}
