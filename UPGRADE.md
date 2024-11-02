# Upgrading
## From v2 to v3
- Now `EnumCollection` is an one-dimensional extension of collection that contains only enum, so you need to review your code if you use this collection in another way.
- We checked, rewrited and tested all collection methods, but in case we have missing something please open a issue.

- this collection methods are not available in `EnumCollection` class: `range`, `median`, `mode`, `crossJoin`, `flip`, `collapse`, `collapseWithKeys`, `pluck`, `mergeRecursive`, `select`, `flatten`, `replaceRecursive`, `sliding`, `dot`, `undot`, `zip`
## From v1 to v2

### Model casting
Simply update the definition of Model casts from

```php
    protected $casts = [
        'field_name' => EnumCollections::class,
    ];
    
    public array $enumCollections = [
        'field_name' => FieldEnum::class,
    ];
```
to
```php
    use Datomatic\EnumCollections\Casts\AsLaravelEnumCollection;
    ...
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
### EnumCollection

Where you use `new EnumCollection(...)` replace with `EnumCollection::from(...)` or `EnumCollection::tryFrom(...)`.


### HasEnumCollection trait

- rename `whereEnumCollectionContains` to `whereContains`
- rename `whereEnumCollectionDoesntContain` to `whereDoesntContain`
- rename `orWhereEnumCollectionContains` to `orWhereContains`
- rename `orWhereEnumCollectionDoesntContain` to `orWhereDoesntContain`
