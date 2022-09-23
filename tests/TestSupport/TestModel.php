<?php

namespace Datomatic\EnumCollections\Tests\TestSupport;

use Datomatic\EnumCollections\Casts\EnumCollections;
use Datomatic\EnumCollections\EnumCollection;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\IntBackedEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\PureEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\StringBackedEnum;
use Datomatic\EnumCollections\Traits\HasEnumCollections;
use Illuminate\Database\Eloquent\Model;

/**
 * @property EnumCollection<IntBackedEnum> $visibilities
 * @property EnumCollection<PureEnum> $colors
 * @property EnumCollection<StringBackedEnum> $sizes
 * @property array<string,string> $enumCollections
 */
class TestModel extends Model
{
    use HasEnumCollections;

    protected $table = 'test_models';

    protected $guarded = [];
    public $timestamps = false;

    protected $casts = [
        'visibilities' => EnumCollections::class,
        'colors' => EnumCollections::class,
        'sizes' => EnumCollections::class
    ];

    public array $enumCollections = [
        'visibilities' => IntBackedEnum::class,
        'colors' => PureEnum::class,
        'sizes' => StringBackedEnum::class
    ];
}
