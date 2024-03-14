# Changelog

All notable changes to `laravel-enum-collections` will be documented in this file.

## v2.0.1 - 2024-03-14

- rename `whereEnumCollectionContains` to `whereContains`
- rename `whereEnumCollectionDoesntContain` to `whereDoesntContain`
- rename `orWhereEnumCollectionContains` to `orWhereContains`
- rename `orWhereEnumCollectionDoesntContain` to `orWhereDoesntContain`
- add field array cast compatibility with above methods

## v2.0.0 - 2024-03-14

Upgrade to v2 (please read [UPGRADE.md](https://github.com/datomatic/laravel-enum-collections/blob/main/UPGRADE.md) file)

- add more elegant way to create an `EnumCollection` with `tryFrom` and `from` method

```php
EnumCollection::of(Enum::class)->from($data);
EnumCollection::of(Enum::class)->tryFrom($data);
EnumCollection::from($data, Enum::class);
EnumCollection::tryFrom($data, Enum::class);


```
- change casting definition in only onle line inside `casts` model property

```php
//Laravel 9/10
protected $casts = [
    'field_name' => AsLaravelEnumCollection::class.':'.FieldEnum::class,
];

//Laravel 11
protected function casts(): array
{
    return [
        'field_name' => AsLaravelEnumCollection::of(FieldEnum::class),
    ];
}


```
- add Laravel 11 support
- refactoring

## v1.1.1 - 2024-01-04

- remove phpdoc on EnumCollections

## v1.1.0 - 2023-07-29

- add `whereEnumCollectionDoesntContain` and `orWhereEnumCollectionDoesntContain` method on trait
- accept on all where methods array and collection

## v1.0.2 - 2023-02-15

Laravel 10 support

## v1.0.0 - 2022-09-27

🚀 First Release
