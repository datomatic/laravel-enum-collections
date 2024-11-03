# Changelog

All notable changes to `laravel-enum-collections` will be documented in this file.

## v3.1.1 - 2024-11-03

- laravel-enum-helper v2

## v3.1.0 - 2024-11-02

- `whereContainsAny`, `orWhereContainsAny`, `whereDoesntContainAny` and `orWhereDoesntContainAny` scopes on `HasEnumCollections` trait

## v3.0.0 - 2024-11-02

- `EnumCollection` now it's a one-dimensional collection that contains only enums.
- New Unique options on model casting that prevent to have duplciates on `EnumCollection`
- `EnumCollection` new methods: `containsAny`, `doesntContainAny`, `toValues`, `toCollectionValues`, `mapStrict`, `mapWithKeysStrict`
- `EnumCollection` methods not supported `range`, `median`, `mode`, `crossJoin`, `flip`, `collapse`, `collapseWithKeys`, `pluck`, `mergeRecursive`, `select`, `flatten`, `replaceRecursive`, `sliding`, `dot`, `undot`, `zip`
- `EnumCollection` methods that return a normal laravel collection: `map`, `keys`, `mapWithKeys`, `combine`, `mapToDictionary`, `groupBy`, `split`, `splitIn`, `chunk`, `chunkWhile`, `countBy`, `toBase`

## v2.0.3 - 2024-10-03

- fix phpDoc
- type refactor

## v2.0.2 - 2024-04-10

- fix: adding forwarding unhandled __call/__callStatic to EnumCollection parent

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
