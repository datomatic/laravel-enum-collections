![Enum Helper-Dark](branding/dark.png#gh-dark-mode-only)![Enum Helper-Light](branding/light.png#gh-light-mode-only)
# Laravel Enum Collections

[![Latest Version on Packagist](https://img.shields.io/packagist/v/datomatic/laravel-enum-collections.svg?style=for-the-badge)](https://packagist.org/packages/datomatic/laravel-enum-collections)
[![Pest Tests number](https://img.shields.io/static/v1?label=%23tests&message=44&color=FF88FA&style=for-the-badge&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAABiVBMVEUAAAD/iPv9yP3Xm+j/mP//wfVj67Je6bP/h/pVx6p6175d57WQycf+iPn/iPrsnezArd3+t/qpvNJd6LP/jPpu6rv/lPr/kPpc57T/rvtc57Np6rj3oPl37cL/tfn/wv9d6brX//L/g/rYn+n/gvrWm+di6LX+jPrskfGWzMpt6bln4bdd57Jk6LWSycj+vPquwNVo6rde6bP7nvvYnup91b/+vfv/lvtc57OqvNTFs9//t/td57L9t/r/iPpd6LPapej/ovp26bxy67v9lfld6LJr4Ljwsvb/xv3/jv39zv1t6buG5cTDreH5ivlc5rJy676V4cxb57D/y/h50MOy4OCUxcVa77X/iPpe6LP/jP+pu9L8t///tvuQycfArNxp6LzArd151r7/i/9n4bb/j/9e6rT/ifr7ifrskvLYnuhi87tg8blg7bf/vv//lP+wxNtj9b3/qv//oP/+ivz/l/r8ifryn/fvlPTfpPDeofDKtujHtOWX1NF/4seC3cR82sFu7cBo5LiMwPMrAAAAWHRSTlMA/Wv8FAIC/dME/Wj+3tEG/Pv798G1oHRjS0k1LBsWDgsJ/v36+fTy8ezn4+Lh29XNzMzLysLAwLSwr66opJqakY+Ni4J7end0bGlpY11XU048KicmIR8fizl+vwAAAVdJREFUKM9tz2VXAlEQgOFBBURpkE67u7u7E1YFYQl1SbvrlztDiLvss+fc/fCemXMvAEhhKqU759P1rLoxUDUyEh9fPH0z7ALiVrEY+SSNtxNS2upouYv7hOL191aKVsZHUTgbnQPQgDkq4ctHdoQmTWmW4WFzlVUDVpNKXf2fWpWbZIwUq/hcmjWGYnSa1pZZjEoomrEdVAisD7CX6GEb40rqTODxCj21OjDOvjRV8l2jhudBDchg/FUbDIZCITzwQyH6a9+2AMDbm9GfltFnxgAdtQWUgQJl4VQq37uPcSnsfYZzav6Ew18fQ4fUYPM7Qn4uSiIdyx5saJ6T+/3+5KSltshicwI2UpfAKE/aoARTvnn7KMYMdlAUyWRSyHN2JeU42HlCi4TszTHcmuj3iMVdP5JzoyAWNzi6T3ZGrMFCliK3BAqRSC/B2+6IxvYYNcO+2Npfv+yFi10LfBUAAAAASUVORK5CYII=)](https://github.com/datomatic/laravel-enum-collections/tree/main/tests)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/datomatic/laravel-enum-collections/run-tests?label=tests&color=5FE8B3&style=for-the-badge&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAABiVBMVEUAAAD/iPv9yP3Xm+j/mP//wfVj67Je6bP/h/pVx6p6175d57WQycf+iPn/iPrsnezArd3+t/qpvNJd6LP/jPpu6rv/lPr/kPpc57T/rvtc57Np6rj3oPl37cL/tfn/wv9d6brX//L/g/rYn+n/gvrWm+di6LX+jPrskfGWzMpt6bln4bdd57Jk6LWSycj+vPquwNVo6rde6bP7nvvYnup91b/+vfv/lvtc57OqvNTFs9//t/td57L9t/r/iPpd6LPapej/ovp26bxy67v9lfld6LJr4Ljwsvb/xv3/jv39zv1t6buG5cTDreH5ivlc5rJy676V4cxb57D/y/h50MOy4OCUxcVa77X/iPpe6LP/jP+pu9L8t///tvuQycfArNxp6LzArd151r7/i/9n4bb/j/9e6rT/ifr7ifrskvLYnuhi87tg8blg7bf/vv//lP+wxNtj9b3/qv//oP/+ivz/l/r8ifryn/fvlPTfpPDeofDKtujHtOWX1NF/4seC3cR82sFu7cBo5LiMwPMrAAAAWHRSTlMA/Wv8FAIC/dME/Wj+3tEG/Pv798G1oHRjS0k1LBsWDgsJ/v36+fTy8ezn4+Lh29XNzMzLysLAwLSwr66opJqakY+Ni4J7end0bGlpY11XU048KicmIR8fizl+vwAAAVdJREFUKM9tz2VXAlEQgOFBBURpkE67u7u7E1YFYQl1SbvrlztDiLvss+fc/fCemXMvAEhhKqU759P1rLoxUDUyEh9fPH0z7ALiVrEY+SSNtxNS2upouYv7hOL191aKVsZHUTgbnQPQgDkq4ctHdoQmTWmW4WFzlVUDVpNKXf2fWpWbZIwUq/hcmjWGYnSa1pZZjEoomrEdVAisD7CX6GEb40rqTODxCj21OjDOvjRV8l2jhudBDchg/FUbDIZCITzwQyH6a9+2AMDbm9GfltFnxgAdtQWUgQJl4VQq37uPcSnsfYZzav6Ew18fQ4fUYPM7Qn4uSiIdyx5saJ6T+/3+5KSltshicwI2UpfAKE/aoARTvnn7KMYMdlAUyWRSyHN2JeU42HlCi4TszTHcmuj3iMVdP5JzoyAWNzi6T3ZGrMFCliK3BAqRSC/B2+6IxvYYNcO+2Npfv+yFi10LfBUAAAAASUVORK5CYII=)](https://github.com/datomatic/laravel-enum-collections/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/datomatic/laravel-enum-collections/Fix%20PHP%20code%20style%20issues?label=code%20style&color=5FE8B3&style=for-the-badge)](https://github.com/datomatic/laravel-enum-collections/actions/workflows/fix-php-code-style-issues.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/datomatic/laravel-enum-collections.svg?style=for-the-badge)](https://packagist.org/packages/datomatic/laravel-enum-collections)

Save a collection of Enums in an Eloquent field and interact with it.

## Installation

You can install the package via composer:

```bash
composer require datomatic/laravel-enum-collections
```

## Set up

Before you can use this package you must setup the eloquent Model.

### 1. Database Migration 
```php
Schema::table('table', function (Blueprint $table) {
    $table->json('field_name')->nullable()->after('some_field');
});
```

### 2. Model set up

To set up your model you must:
- add a custom cast `EnumCollections::class`
- add an `$enumCollections` array containing the fields and relative enum classes
- add an optional `HasEnumCollections` trait to make query on enum collections fields

You can also set more than one field if you need.

```php

use Datomatic\EnumCollections\Casts\EnumCollections;
use Datomatic\EnumCollections\EnumCollection;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use HasEnumCollections;
    
    protected $casts = [
        'field_name' => EnumCollections::class,
    }
    
    protected array $enumCollections = [
        'field_name' => FieldEnum::class,
    ];
}
```

## Usage

After model set up you can use the package potentials.


### Set the enum collection field

You can set enum collection field passing a single element, a collection or an array of elements.
After the setting, the field will become an `EnumCollection`.

Each element can be an:
- enum object instance
- enum case name string
- enum case value (only for `BackedEnum`)
- enum case (string) value (only for `IntBackedEnum`) 

```php
enum FieldEnum: int
{
    case PRIVATE = 1;
    case PUBLIC = 2;
    case PROTECTED = 3;
}

$model = new TestModel();
$model->field_name = FieldEnum::PRIVATE; // ✅ EnumCollection<FieldEnum::PRIVATE>
$model->field_name = 'PRIVATE'; // ✅ EnumCollection<FieldEnum::PRIVATE>
$model->field_name = 1; // ✅ EnumCollection<FieldEnum::PRIVATE>
$model->field_name = '1'; // ✅ EnumCollection<FieldEnum::PRIVATE>
$model->field_name = [FieldEnum::PRIVATE,FieldEnum::PUBLIC]; // ✅ EnumCollection<FieldEnum>
$model->field_name = collect([FieldEnum::PRIVATE,FieldEnum::PUBLIC]); // ✅ EnumCollection<FieldEnum>
```

### EnumCollection
The `EnumCollection` extend the [Laravel collection](https://laravel.com/docs/collections) and overload the `contains` method to add the compatibility with:
- enum object instance
- enum case name string
- enum case value (only for `BackedEnum`)
- enum case (string) value (only for `IntBackedEnum`)

```php

$model = new TestModel();
$model->field_name = [FieldEnum::PRIVATE,FieldEnum::PUBLIC];

$model->field_name->contains(FieldEnum::PRIVATE); // true
$model->field_name->contains(FieldEnum::PROTECTED); // false
$model->field_name->contains(1); // true
$model->field_name->contains('1'); // true
$model->field_name->contains('PRIVATE'); // true
```

### HasEnumCollections trait
If you include also the `HasEnumCollections` into the model you can query the models with the new where functions `whereEnumCollectionContains` and `orWhereEnumCollectionContains`.

```php
TestModel::whereEnumCollectionContains('field_name', FieldEnum::PRIVATE)->get()

TestModel::whereEnumCollectionContains('field_name', FieldEnum::PRIVATE)
    ->whereEnumCollectionContains('field_name', FieldEnum::PUBLIC)
    ->get()

TestModel::whereEnumCollectionContains('field_name', FieldEnum::PRIVATE)
    ->orWhereEnumCollectionContains('field_name', FieldEnum::PUBLIC)
    ->get()
```
## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Alberto Peripolli](https://github.com/datomatic)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
