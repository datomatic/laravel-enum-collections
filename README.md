![Enum Helper-Dark](branding/dark.png#gh-dark-mode-only)![Enum Helper-Light](branding/light.png#gh-light-mode-only)
# Laravel Enum Collections

[![Latest Version on Packagist](https://img.shields.io/packagist/v/datomatic/laravel-enum-collections.svg?style=for-the-badge)](https://packagist.org/packages/datomatic/laravel-enum-collections)
[![Pest Tests number](https://img.shields.io/static/v1?label=%23tests&message=112&color=D1529F&style=for-the-badge&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAZCAMAAAAVHr4VAAACylBMVEUAAABGv+ZGwOZHv+ZQwOhHwOdfrt2WistGvuZoqNpsptp5ndSCmNLkV69yoteehchVtuJ4ndVGv+dpqNmAmdCjgMRjq9xupdhNuuSlgMVvo9dLvORpp9l9m9Ncsd+Dl9GNkM5Gv+ZGwOe5c75kq9x5ndWIlM+ehci0d8FHv+dGv+dGveRFv+eSjcq2csDFa7rJabpVtuE8tPFXtOFWtOBbsd6Lkc7uUayCl9HVYLRjq9uGldCNkM6UjMybh8migsaqfcOxeMG4dL6/b7zGarrOZrf3SqlirNz0TKnbXLJNu+RtpdlzodaYicqfhMamgMSte8K0dr+8cb3DbbtGv+VlqtulgsXKaLhUt+FVtOBlq9xHwOeAmdH9R6d6nNSsfMJgrt5csN5Gv+ZGwOfYX7OIk87/RaVGvubhWrFas+BvpNiTjMyof8NSuOJKvOVkq9tUt+JxoddLuuSQjsxZs+G9cr1Hv+ZTteBXteFFv+Ztp9hHvuTpUq1IveSWh8lQt+NcsODyT6t0oddIvOOhgMhvotVGueX1R6N6m9Oqd7tGv+ZGv+dMzvlIveVN0v1Ixe2IlNCPj81KyfJPuuRYs+Bird1N0fxHwupUt+NRuOJqsOJcsd9qp9p0odd3n9V9m9OAmdKLks+vhM2TjcyYicq8cb3DbLvSYrbXX7TeWrJSzvtpxPdIxu9NxO9YwO1GwOhjt+hts+hzrON6p9+Ao91lrNyKndpxo9iSmNiZk9WhjtOFltCdiMqhh8qig8angMWrfMOuesK0dsC4dL/IabrLZ7jQZLf/TLLoVK7wUK1ZyvhgxPVlvO53te1eu+pOv+lQvOdVuuZ9rOVYteFds+GLod9zqd6Wnd6Eot1ypdqdldjQg9h8ntd8ndW9hNKpidCnic+2f8r2acm9ese0fMbFdcXMcMKyeMHUa8DAb7zVZLjvW7j1VbTaXbPpOP0KAAAAinRSTlMA/faCBf7+/vn+/v7+/v39+Pf0MzIy/v79/fr29vTz8u/Mvp+VlZWVlIh2RzUyMjIsIQj9/fr6+vn5+Pj4+Pj4+Pj4+Pj4+Pb08vDv7+/v7+/v7+/u7urq5dva1dTTyMjHubeyr62qmpeWlZWVk5GPhoWCgXZ0cGtnZGJeXVVVQDo6OS4uHh0ZFw81+7MoAAACAklEQVQoz2IAgbTjbidP8L5dfnTZ0sMH9+9bsnihfFxhKwMLWDL2uPmJlStAcocO7udfMnECz51LZ6MawHLtHid5V644dgQod0CSn33ihAXcfDJWqyMNQZKNb8ByLw8/P7BvD0hu3vY5U00FVmmCJGtee1q6ujg7OTrY29naWMsKSnFOnTJDukcRJJnx6NWLZ08fP1z0YNfOHdu2bN40i23KzN6u9UwgyZTl4QAFBwUG+Pv5KijIy/p4h8oZT+7tYuwPAUka6Ovr6YmKiurqioiICAsLG5ZfYGXsZupRYsAGans4WLs4etJBGpU1NHLU1bPV1FRVs1RUMnPzFNcwdjEyTtICSpbefrLoPsgpNzZv2rjh3Lq1k/oZu7o4+jm0gZLK9yT3AkOM5+5crq2zL0/u7e7uYuwCWQkKvvhFoFDh4ebjEgOMzUSilxEIutYL9XeDNHaG7QWHGDBUZs+Y3A0GjELTe0A2MrTsWbxw9/x5UjLucgLSQkxgwDqtv4wBFCt1t8yAcuKzVp8+taoPBKZN65mepAOWYyi+bjF/rvishJKCImYoqGyG+Tj1qiDQmRsqsAZHzBa+OWJXZtQDjcEAHYI3OafOvujVhk2y6RonMILWRmM1tWojKPImJWPTyJB/RgAYeZM0GYywSCauk+jq6l5TjVUy4jwweqaz6mAzFgCjl8baxN3bSgAAAABJRU5ErkJggg==)](https://github.com/datomatic/laravel-enum-collections/tree/main/tests)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/datomatic/laravel-enum-collections/run-tests.yml?branch=main&label=tests&color=7DDFA8&style=for-the-badge&logo=data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAZCAMAAAAVHr4VAAACylBMVEUAAABGv+ZGwOZHv+ZQwOhHwOdfrt2WistGvuZoqNpsptp5ndSCmNLkV69yoteehchVtuJ4ndVGv+dpqNmAmdCjgMRjq9xupdhNuuSlgMVvo9dLvORpp9l9m9Ncsd+Dl9GNkM5Gv+ZGwOe5c75kq9x5ndWIlM+ehci0d8FHv+dGv+dGveRFv+eSjcq2csDFa7rJabpVtuE8tPFXtOFWtOBbsd6Lkc7uUayCl9HVYLRjq9uGldCNkM6UjMybh8migsaqfcOxeMG4dL6/b7zGarrOZrf3SqlirNz0TKnbXLJNu+RtpdlzodaYicqfhMamgMSte8K0dr+8cb3DbbtGv+VlqtulgsXKaLhUt+FVtOBlq9xHwOeAmdH9R6d6nNSsfMJgrt5csN5Gv+ZGwOfYX7OIk87/RaVGvubhWrFas+BvpNiTjMyof8NSuOJKvOVkq9tUt+JxoddLuuSQjsxZs+G9cr1Hv+ZTteBXteFFv+Ztp9hHvuTpUq1IveSWh8lQt+NcsODyT6t0oddIvOOhgMhvotVGueX1R6N6m9Oqd7tGv+ZGv+dMzvlIveVN0v1Ixe2IlNCPj81KyfJPuuRYs+Bird1N0fxHwupUt+NRuOJqsOJcsd9qp9p0odd3n9V9m9OAmdKLks+vhM2TjcyYicq8cb3DbLvSYrbXX7TeWrJSzvtpxPdIxu9NxO9YwO1GwOhjt+hts+hzrON6p9+Ao91lrNyKndpxo9iSmNiZk9WhjtOFltCdiMqhh8qig8angMWrfMOuesK0dsC4dL/IabrLZ7jQZLf/TLLoVK7wUK1ZyvhgxPVlvO53te1eu+pOv+lQvOdVuuZ9rOVYteFds+GLod9zqd6Wnd6Eot1ypdqdldjQg9h8ntd8ndW9hNKpidCnic+2f8r2acm9ese0fMbFdcXMcMKyeMHUa8DAb7zVZLjvW7j1VbTaXbPpOP0KAAAAinRSTlMA/faCBf7+/vn+/v7+/v39+Pf0MzIy/v79/fr29vTz8u/Mvp+VlZWVlIh2RzUyMjIsIQj9/fr6+vn5+Pj4+Pj4+Pj4+Pj4+Pb08vDv7+/v7+/v7+/u7urq5dva1dTTyMjHubeyr62qmpeWlZWVk5GPhoWCgXZ0cGtnZGJeXVVVQDo6OS4uHh0ZFw81+7MoAAACAklEQVQoz2IAgbTjbidP8L5dfnTZ0sMH9+9bsnihfFxhKwMLWDL2uPmJlStAcocO7udfMnECz51LZ6MawHLtHid5V644dgQod0CSn33ihAXcfDJWqyMNQZKNb8ByLw8/P7BvD0hu3vY5U00FVmmCJGtee1q6ujg7OTrY29naWMsKSnFOnTJDukcRJJnx6NWLZ08fP1z0YNfOHdu2bN40i23KzN6u9UwgyZTl4QAFBwUG+Pv5KijIy/p4h8oZT+7tYuwPAUka6Ovr6YmKiurqioiICAsLG5ZfYGXsZupRYsAGans4WLs4etJBGpU1NHLU1bPV1FRVs1RUMnPzFNcwdjEyTtICSpbefrLoPsgpNzZv2rjh3Lq1k/oZu7o4+jm0gZLK9yT3AkOM5+5crq2zL0/u7e7uYuwCWQkKvvhFoFDh4ebjEgOMzUSilxEIutYL9XeDNHaG7QWHGDBUZs+Y3A0GjELTe0A2MrTsWbxw9/x5UjLucgLSQkxgwDqtv4wBFCt1t8yAcuKzVp8+taoPBKZN65mepAOWYyi+bjF/rvishJKCImYoqGyG+Tj1qiDQmRsqsAZHzBa+OWJXZtQDjcEAHYI3OafOvujVhk2y6RonMILWRmM1tWojKPImJWPTyJB/RgAYeZM0GYywSCauk+jq6l5TjVUy4jwweqaz6mAzFgCjl8baxN3bSgAAAABJRU5ErkJggg==)](https://github.com/datomatic/laravel-enum-collections/actions/workflows/run-tests.yml)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/datomatic/laravel-enum-collections/phpstan.yml?label=code%20style&color=7DDFA8&style=for-the-badge)](https://github.com/datomatic/laravel-enum-collections/actions/workflows/fix-php-code-style-issues.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/datomatic/laravel-enum-collections.svg?style=for-the-badge)](https://packagist.org/packages/datomatic/laravel-enum-collections)

A Laravel Collection extension to store enums with a useful eloquent fields cast.  
Take your interaction with enums to the next level.  
Compatible with `PureEnum`, `BackedEnum` and [`datomatic/laravel-enum-helper`](https://github.com/datomatic/laravel-enum-helper) package.

## Installation

You can install the package via composer:

```bash
composer require datomatic/laravel-enum-collections
```

## EnumCollection

`EnumCollection` is an extension of base [Laravel collection](https://laravel.com/docs/collections) that expand his functionalities and overload the `contains` method to add the compatibility with:
- enum object instance
- enum case name string
- enum case value (only for `BackedEnum`)
- enum case (string) value (only for `IntBackedEnum`)

### Creating an EnumCollection

You can set enum collection field passing in 4 ways:
```php
use \Datomatic\EnumCollections\EnumCollection;

EnumCollection::of(Enum::class)->from($data);
EnumCollection::of(Enum::class)->tryFrom($data);
EnumCollection::from($data, Enum::class);
EnumCollection::tryFrom($data, Enum::class);
```
`from` method throw an `ValueError` Exception if an element in `$data` is wrong, instead `tryFrom` skips bad data without raise exceptions.  
`$data` can be a single element or a `collection`, `array`,... of elements.

If `$data` contains only Enum elements you can also omit the `EnumClass` (the collection take the EnumClass of the first element).
```php
EnumCollection::from(Enum::CASE1); // ✅ EnumCollection<Enum::CASE1>
EnumCollection::from('CASE1', Enum::class); // ✅ EnumCollection<Enum::CASE1>
EnumCollection::from(1, Enum::class); // ✅ EnumCollection<Enum::CASE1>
EnumCollection::from('1', Enum::class); // ✅ EnumCollection<Enum::CASE1>
EnumCollection::from([Enum::CASE1,Enum::CASE2]); // ✅ EnumCollection<Enum>
EnumCollection::from(collect([Enum::CASE1,Enum::CASE2])); // ✅ EnumCollection<Enum>
```

### `Contains' method

```php
use \Datomatic\EnumCollections\EnumCollection;

$enumCollection = EnumCollection::from([Enum::CASE1,Enum::CASE2]); // [1,2]

$enumCollection->contains(Enum::CASE1); // true
$enumCollection->contains(Enum::CASE3); // false
$enumCollection->doesntContain(Enum::CASE3); // true
$enumCollection->contains(1); // true
$enumCollection->contains('1'); // true
$enumCollection->contains('PRIVATE'); // true
$enumCollection->doesntContain('PRIVATE'); // false
```

### `toValues' method
`toValues` method serialize the collection content, if the element is a `PureEnum` will be pass the `name` of the case, otherwise the `value`.

```php
use \Datomatic\EnumCollections\EnumCollection;

EnumCollection::from([Enum::CASE1,Enum::CASE2,Enum::CASE2])->toValues(); // [1,2,2]
EnumCollection::from(['CASE1','CASE2','CASE2'],Enum::class)->toValues(); // [1,2,2]
EnumCollection::from([1,2,2],Enum::class)->toValues(); // [1,2,2]
EnumCollection::from(['1','2','2'],Enum::class)->toValues(); // [1,2,2]
```

## Casting

Before you can use this casting option you must config the eloquent Model.

### 1. Database Migration 
```php
Schema::table('table', function (Blueprint $table) {
    $table->json('field_name')->nullable()->after('some_field');
});
```

### 2. Model set up

To set up your model you must:
- add a custom cast `AsEnumCollections::class` with the enumClass as attribute
- add an optional `HasEnumCollections` trait to make query on enum collections fields

You can also set more than one field if you need.

```php

use Datomatic\EnumCollections\Casts\AsEnumCollection;
use Datomatic\EnumCollections\EnumCollection;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    use HasEnumCollections;
    
    //Laravel 9/10
    protected $casts = [
        'field_name' => AsEnumCollection::class.':'.FieldEnum::class,
    ];
    
    //Laravel 11
    protected function casts(): array
    {
        return [
            'field_name' => AsEnumCollection::of(FieldEnum::class),
       ];
    }
}
```

### Set the enum collection field

You can set enum collection field passing a single element, a collection or an array of elements.
After the setting, the field will become an `EnumCollection`.

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

### Database saved data
A serialization of enumCollection is saved in the database, if the element is a `PureEnum` will be saved the `name` of the case, otherwise the `value`.

### EnumCollection
Thanks to casting you can interact with `field_name` like a normal `EnumCollection` with all functionalities showed before.

```php

$model = new TestModel();
$model->field_name = [FieldEnum::PRIVATE,FieldEnum::PUBLIC];

$model->field_name->contains(FieldEnum::PRIVATE); // true
$model->field_name->contains(FieldEnum::PROTECTED); // false
$model->field_name->contains(1); // true
$model->field_name->contains('1'); // true
$model->field_name->contains('PRIVATE'); // true
$model->field_name->doesntContain('PRIVATE'); // false
$model->field_name->doesntContain(FieldEnum::PROTECTED); // true
```

### HasEnumCollections trait
If you include also the `HasEnumCollections` into the model you can query the models with the new where functions `whereEnumCollectionContains`, `orWhereEnumCollectionContains`, `whereEnumCollectionDoesntContain` and `orWhereEnumCollectionDoesntContain`.

```php
TestModel::whereEnumCollectionContains('field_name', FieldEnum::PRIVATE)->get()
TestModel::whereEnumCollectionDoesntContain('field_name', FieldEnum::PRIVATE)->get()

TestModel::whereEnumCollectionContains('field_name', 1)
    ->whereEnumCollectionContains('field_name', FieldEnum::PUBLIC)
    ->get()
    
TestModel::whereEnumCollectionContains('field_name', [FieldEnum::PRIVATE,FieldEnum::PUBLIC)
    ->get()
TestModel::whereEnumCollectionContains('field_name', collect([FieldEnum::PRIVATE,FieldEnum::PUBLIC))
    ->get()
 TestModel::whereEnumCollectionContains('field_name', EnumCollection::make([FieldEnum::PRIVATE,FieldEnum::PUBLIC))
    ->get()
    
TestModel::whereEnumCollectionContains('field_name', [1,2])
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
