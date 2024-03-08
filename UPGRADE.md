# Upgrading

## From v1 to v2

Simply update the Models casts definition from

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
```
