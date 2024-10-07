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
 */
class TestModel extends Model
{
    use HasEnumCollections;

    protected $table = 'test_models';

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'visibilities' => AsLaravelEnumCollection::class.':'.IntBackedEnum::class,
        'colors' => AsLaravelEnumCollection::class.':'.PureEnum::class,
        'sizes' => AsLaravelEnumCollection::class.':'.StringBackedEnum::class,
        'permissions' => AsLaravelEnumCollection::class.':'.LaravelEnum::class,
        'json' => 'array',
    ];
}
