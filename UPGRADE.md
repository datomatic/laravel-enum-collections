# Upgrading

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
