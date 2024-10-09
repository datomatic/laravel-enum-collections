<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Tests\TestSupport;

use Datomatic\EnumCollections\Casts\AsLaravelEnumCollection;
use Datomatic\EnumCollections\EnumCollection;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\IntBackedEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\LaravelEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\PureEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\StringBackedEnum;
use Datomatic\EnumCollections\Traits\HasEnumCollections;
use Illuminate\Database\Eloquent\Model;

/**
 * @property EnumCollection<IntBackedEnum> $visibilities
 * @property EnumCollection<LaravelEnum> $permissions
 * @property EnumCollection<PureEnum> $colors
 * @property EnumCollection<StringBackedEnum> $sizes
 * @property array $json
 * 
 * basically used just to test the AsLaravelEnumCollection cast with laravel ^11.0 syntax
 */
class TestModel11 extends Model
{
    use HasEnumCollections;

    protected $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;

    protected function casts()
    {
        return [
            'visibilities' => AsLaravelEnumCollection::of(IntBackedEnum::class, true),
            'colors' => AsLaravelEnumCollection::of(PureEnum::class, true),
            'sizes' => AsLaravelEnumCollection::of(StringBackedEnum::class, false),
            'permissions' => AsLaravelEnumCollection::of(LaravelEnum::class),
            'json' => 'array',
        ];

    }
}
