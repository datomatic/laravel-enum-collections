<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Traits;

use BackedEnum;
use Datomatic\EnumCollections\Casts\AsLaravelEnumCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use UnitEnum;

/**
 * @method static Builder|self whereContains(string $key, $value)
 * @method static Builder|self orWhereContains(string $key, $value)
 * @method static Builder|self whereDoesntContain(string $key, $value)
 * @method static Builder|self orWhereDoesntContain(string $key, $value)
 * @method static Builder|self whereContainsAny(string $key, $value)
 * @method static Builder|self orWhereContainsAny(string $key, $value)
 * @method static Builder|self whereDoesntContainAny(string $key, $value)
 * @method static Builder|self orWhereDoesntContainAny(string $key, $value)
 */
trait HasEnumCollections
{
    public function scopeWhereContains(Builder $query, string $key, $value): Builder
    {
        return $this->prepareEnumCollectionScopeQuery($query, $key, $value,
            fn (Builder $query, $value) => $query->whereJsonContains($key, $value)
        );
    }

    public function scopeOrWhereContains(Builder $query, string $key, $value): Builder
    {
        return $this->prepareEnumCollectionScopeQuery($query, $key, $value,
            fn (Builder $query, $value) => $query->orWhereJsonContains($key, $value)
        );
    }

    public function scopeWhereDoesntContain(Builder $query, string $key, $value): Builder
    {
        return $this->prepareEnumCollectionScopeQuery($query, $key, $value,
            fn (Builder $query, $value) => $query->whereJsonDoesntContain($key, $value)
        );
    }

    public function scopeOrWhereDoesntContain(Builder $query, string $key, $value): Builder
    {
        return $this->prepareEnumCollectionScopeQuery($query, $key, $value,
            fn (Builder $query, $value) => $query->orWhereJsonDoesntContain($key, $value)
        );
    }

    public function scopeWhereContainsAny(Builder $query, string $key, $value): Builder
    {
        return $this->prepareEnumCollectionArrayScopeQuery($query, $key, $value,
            fn (Builder $query, $values) => $query->where(function (Builder $query) use ($key, $values) {
                foreach ($values as $value) {
                    $query->whereJsonContains($key, $value, 'or');
                }
            })
        );
    }

    public function scopeOrWhereContainsAny(Builder $query, string $key, $value): Builder
    {
        return $this->prepareEnumCollectionArrayScopeQuery($query, $key, $value,
            fn (Builder $query, $values) => $query->orWhere(function (Builder $query) use ($key, $values) {
                foreach ($values as $value) {
                    $query->whereJsonContains($key, $value, 'or');
                }
            })
        );
    }

    public function scopeWhereDoesntContainAny(Builder $query, string $key, $value): Builder
    {
        return $this->prepareEnumCollectionArrayScopeQuery($query, $key, $value,
            fn (Builder $query, $values) => $query->where(function (Builder $query) use ($key, $values) {
                foreach ($values as $value) {
                    $query->whereJsonDoesntContain($key, $value);
                }
            })
        );
    }

    public function scopeOrWhereDoesntContainAny(Builder $query, string $key, $value): Builder
    {
        return $this->prepareEnumCollectionArrayScopeQuery($query, $key, $value,
            fn (Builder $query, $values) => $query->orWhere(function (Builder $query) use ($key, $values) {
                foreach ($values as $value) {
                    $query->whereJsonDoesntContain($key, $value);
                }
            })
        );
    }

    public function hasEnumCollectionOrJsonCast(string $key): bool
    {
        $casts = $this->getCasts();

        if (isset($casts[$key]) &&
            ($this->isJsonCastable($key) || str($casts[$key])->contains(AsLaravelEnumCollection::class))
        ) {
            return true;
        }

        return false;
    }

    private function prepareEnumCollectionScopeQuery(Builder $query, string $key, mixed $value, callable $closure): Builder
    {
        if ($this->hasEnumCollectionOrJsonCast($key)) {
            return $closure($query, $this->getValue($value));
        }

        return $query;
    }

    private function prepareEnumCollectionArrayScopeQuery(Builder $query, string $key, mixed $value, callable $closure): Builder
    {
        if ($this->hasEnumCollectionOrJsonCast($key)) {
            return $closure($query, Arr::wrap($this->getValue($value)));
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
