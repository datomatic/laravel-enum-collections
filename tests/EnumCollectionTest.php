<?php

declare(strict_types=1);

use Datomatic\EnumCollections\EnumCollection;
use Datomatic\EnumCollections\Exceptions\MethodNotSupported;
use Datomatic\EnumCollections\Exceptions\MissingEnumClass;
use Datomatic\EnumCollections\Exceptions\ValueError;
use Datomatic\EnumCollections\Exceptions\WrongEnumClass;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\IntBackedEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\PureEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\StringBackedEnum;
use Datomatic\EnumCollections\Tests\TestSupport\TestModel;

test('enumCollection can accept an EnumCollection on constructor', function () {
    $enumCollection = new EnumCollection([PureEnum::BLACK, PureEnum::RED]);
    $enumCollection2 = new EnumCollection($enumCollection);

    expect($enumCollection2->getEnumClass())->toBe(PureEnum::class);
    expect($enumCollection2->toArray())->toBe([PureEnum::BLACK, PureEnum::RED]);
});

test('enumCollection can accept only one level array', function ($from, string $class, int $results) {
    $enumCollection = EnumCollection::of($class)->from($from);
    $enumCollection2 = EnumCollection::of($class)->tryFrom($from);
    $enumCollection3 = new EnumCollection($from, $class);

    expect($enumCollection->count())->toBe($results);
    expect($enumCollection2->count())->toBe($results);
    expect($enumCollection3->count())->toBe($results);
})->with([
    'enum multidimensional array' => [[3 => [PureEnum::BLACK, PureEnum::RED], PureEnum::GREEN], PureEnum::class, 3],
]);

it('return an exception if a wrong enum class passed', function () {
    expect(fn() => EnumCollection::of('WrongClass'))->toThrow(WrongEnumClass::class);
    expect(fn() => EnumCollection::of(TestModel::class))->toThrow(WrongEnumClass::class);
    expect(fn() => new EnumCollection([], 'WrongClass'))->toThrow(WrongEnumClass::class);
    expect(fn() => new EnumCollection([], TestModel::class))->toThrow(WrongEnumClass::class);
    expect(fn() => new EnumCollection([], TestModel::class))->toThrow(WrongEnumClass::class);
});

test('enumCollection can accept an array of enums', function ($from, array $results) {
    $enumCollection = EnumCollection::from($from);
    $enumCollection2 = EnumCollection::tryFrom($from);
    $enumCollection3 = new EnumCollection($from);

    expect($enumCollection)->toBeInstanceOf(EnumCollection::class);
    expect($enumCollection->toArray())->toEqual($results);
    expect($enumCollection2)->toBeInstanceOf(EnumCollection::class);
    expect($enumCollection2->toArray())->toEqual($results);
    expect($enumCollection3)->toBeInstanceOf(EnumCollection::class);
    expect($enumCollection3->toArray())->toEqual($results);
})->with([
    'enum single' => [PureEnum::BLACK, [PureEnum::BLACK]],
    'enum array' => [[PureEnum::BLACK, PureEnum::GREEN], [PureEnum::BLACK, PureEnum::GREEN]],
    'string enum array' => [
        [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::MEDIUM],
        [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::MEDIUM]
    ],
    'int enum array' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC], [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]
    ],
]);

test('enumCollection throws an exception if an enum class is not set and an array of values/names is passed',
    function ($from) {
        expect(fn() => EnumCollection::from($from))->toThrow(MissingEnumClass::class);
        expect(fn() => EnumCollection::tryFrom($from))->toThrow(MissingEnumClass::class);
        expect(fn() => new EnumCollection($from))->toThrow(MissingEnumClass::class);
    })->with([
    //    'enum single' => ['BLACK'],
    //    'enum array' => [['BLACK', 'GREEN']],
    'string enum array' => [['S', 'M', 'L']],
    //    'int enum array' => [[1, 2, 3]],
]);

test('enumCollection can accept an array of enums values and names',
    function ($from, string $enumClass, array $results) {
        $enumCollection = EnumCollection::of($enumClass)->from($from);
        $enumCollection2 = EnumCollection::of($enumClass)->tryFrom($from);
        $enumCollection3 = new EnumCollection($from, $enumClass);

        expect($enumCollection)->toBeInstanceOf(EnumCollection::class);
        expect($enumCollection->toArray())->toEqual($results);

        expect($enumCollection2)->toBeInstanceOf(EnumCollection::class);
        expect($enumCollection2->toArray())->toEqual($results);

        expect($enumCollection3)->toBeInstanceOf(EnumCollection::class);
        expect($enumCollection3->toArray())->toEqual($results);
    })->with([
    'enum single' => ['BLACK', PureEnum::class, [PureEnum::BLACK]],
    'enum array' => [['BLACK', 'GREEN'], PureEnum::class, [PureEnum::BLACK, PureEnum::GREEN]],
    'string enum array' => [
        ['S', 'M', 'M'], StringBackedEnum::class,
        [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::MEDIUM]
    ],
    'string enum array2' => [
        ['SMALL', 'MEDIUM', 'MEDIUM'], StringBackedEnum::class,
        [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::MEDIUM]
    ],
    'int enum array' => [[1, 2], IntBackedEnum::class, [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
    'int enum array2' => [['1', '2'], IntBackedEnum::class, [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
    'int enum array3' => [['PRIVATE', 'PUBLIC'], IntBackedEnum::class, [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
]);

test('enumCollection throws an exception if wrong className passed with from', function ($from, string $enumClass) {
    expect(fn() => EnumCollection::of($enumClass)->from($from))->toThrow(ValueError::class);
    expect(fn() => new EnumCollection($from, $enumClass))->toThrow(ValueError::class);
})->with([
    'enum single' => ['BLACK', StringBackedEnum::class],
    'enum array' => [['BLACK', 'GREEN'], IntBackedEnum::class],
    'string enum array' => [['S', 'M', 'M'], PureEnum::class],
    'int enum array' => [[1, 2], PureEnum::class],
]);

test('enumCollection doesnt throws an exception if wrong className passed with tryFrom',
    function ($from, string $enumClass) {
        expect(fn() => EnumCollection::of($enumClass)->tryFrom($from))->not->toThrow(ValueError::class);
    })->with([
    'enum single' => ['BLACK', StringBackedEnum::class],
    'enum array' => [['BLACK', 'GREEN'], IntBackedEnum::class],
    'string enum array' => [['S', 'M', 'M'], PureEnum::class],
    'int enum array' => [[1, 2], PureEnum::class],
]);

test('enumCollection throws an exception if wrong value/name passed with from', function ($from, string $enumClass) {
    expect(fn() => EnumCollection::of($enumClass)->from($from))->toThrow(ValueError::class);
    expect(fn() => new EnumCollection($from, $enumClass))->toThrow(ValueError::class);
})->with([
    'enum single' => ['SS', StringBackedEnum::class],
    'enum array' => [['EFF', '3493400'], IntBackedEnum::class],
    'string enum array' => [['XC', 'M', 'M'], PureEnum::class],
    'int enum array' => [[33, 2], PureEnum::class],
]);

test('enumCollection throws an exception if wrong value/name passed with tryFrom', function ($from, string $enumClass) {
    expect(fn() => EnumCollection::of($enumClass)->tryFrom($from))->not->toThrow(ValueError::class);
})->with([
    'enum single' => ['SS', StringBackedEnum::class],
    'enum array' => [['EFF', '3493400'], IntBackedEnum::class],
    'string enum array' => [['XC', 'M', 'M'], PureEnum::class],
    'int enum array' => [[33, 2], PureEnum::class],
]);

it('can enumCollection get enumClass', function (?string $enumClass) {
    expect(EnumCollection::of($enumClass)->getEnumClass())->toEqual($enumClass);
    expect((new EnumCollection([], $enumClass))->getEnumClass())->toEqual($enumClass);
})->with([
    'base enum' => [PureEnum::class],
    'string enum array' => [StringBackedEnum::class],
    'int enum array' => [IntBackedEnum::class],
]);

it('throws MissingEnumClass if pass null to of function', function () {
    expect(fn() => EnumCollection::of(null))->toThrow(MissingEnumClass::class);
});

test('enumCollection toValues method', function ($from, ?string $enumClass, array $results) {
    expect(EnumCollection::from($from, $enumClass)->toValues())->toEqual($results);
    expect(EnumCollection::tryFrom($from, $enumClass)->toValues())->toEqual($results);
    expect((new EnumCollection($from, $enumClass))->toValues())->toEqual($results);

    if ($enumClass !== null) {
        expect(EnumCollection::of($enumClass)->from($from)->toValues())->toEqual($results);
        expect(EnumCollection::of($enumClass)->tryFrom($from)->toValues())->toEqual($results);
    }
})->with([
    'enum single' => ['BLACK', PureEnum::class, ['BLACK']],
    'enum single2' => [PureEnum::BLACK, null, ['BLACK']],
    'enum array' => [['BLACK', 'GREEN'], PureEnum::class, ['BLACK', 'GREEN']],
    'enum array2' => [[PureEnum::BLACK, PureEnum::GREEN], null, ['BLACK', 'GREEN']],
    'string enum array' => [['S', 'M', 'M'], StringBackedEnum::class, ['S', 'M', 'M']],
    'string enum array2' => [['SMALL', 'MEDIUM', 'MEDIUM'], StringBackedEnum::class, ['S', 'M', 'M']],
    'string enum array3' => [
        [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::MEDIUM], null, ['S', 'M', 'M']
    ],
    'int enum array' => [[1, 2], IntBackedEnum::class, [1, 2]],
    'int enum array2' => [['1', '2'], IntBackedEnum::class, [1, 2]],
    'int enum array3' => [['PRIVATE', 'PUBLIC'], IntBackedEnum::class, [1, 2]],
    'int enum array4' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC], null, [1, 2]],
]);

it('will can check if EnumCollection contains enum', function ($from, $search, $result) {
    $enumCollection = EnumCollection::from($from);
    $enumCollection2 = EnumCollection::tryFrom($from);
    $enumCollection3 = new EnumCollection($from);

    expect($enumCollection->contains($search))->toEqual($result);
    expect($enumCollection->doesntContain($search))->toEqual(!$result);

    expect($enumCollection2->contains($search))->toEqual($result);
    expect($enumCollection2->doesntContain($search))->toEqual(!$result);

    expect($enumCollection3->contains($search))->toEqual($result);
    expect($enumCollection3->doesntContain($search))->toEqual(!$result);
})->with([
    'pure enum collection search value' => [[PureEnum::GREEN, PureEnum::BLACK], 'GREEN', true],
    'pure enum collection search invalid value' => [[PureEnum::GREEN, PureEnum::BLACK], 'PURPLE', false],
    'pure enum collection search invalid value int' => [[PureEnum::GREEN, PureEnum::BLACK], 1, false],
    'pure enum collection search enum' => [[PureEnum::GREEN, PureEnum::BLACK], PureEnum::BLACK, true],
    'pure enum collection search invalid enum' => [[PureEnum::GREEN, PureEnum::BLACK], PureEnum::YELLOW, false],
    'pure enum collection search name' => [[PureEnum::GREEN, PureEnum::BLACK], 'BLACK', true],
    'pure enum collection search invalid name' => [[PureEnum::GREEN, PureEnum::BLACK], 'YELLOW', false],

    'int enum collection search value' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 1, true],
    'int enum collection search value string' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], '3', true],
    'int enum collection search invalid value' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'A', false],
    'int enum collection search invalid value2' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 4, false],
    'int enum collection search enum' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], IntBackedEnum::PROTECTED, true
    ],
    'int enum collection search invalid enum' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], IntBackedEnum::PUBLIC, false
    ],
    'int enum collection search name' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'PROTECTED', true],
    'int enum collection search invalid name' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'PUBLIC', false],

    'string enum collection search value' => [[StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'L', true],
    'string enum collection search invalid value' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'LD', false
    ],
    'string enum collection search invalid value int' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 4, false
    ],
    'string enum collection search enum' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], StringBackedEnum::EXTRA_LARGE, true
    ],
    'string enum collection search invalid enum' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], StringBackedEnum::SMALL, false
    ],
    'string enum collection search name' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'EXTRA_LARGE', true
    ],
    'string enum collection search invalid name' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'SMALL', false
    ],
]);

it('supports contains', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK]);

    expect($collection->contains('GREEN'))->toBeTrue();
    expect($collection->contains('PURPLE'))->toBeFalse();
    expect($collection->contains(fn($enum) => $enum === PureEnum::GREEN))->toBeTrue();
    expect($collection->contains(fn($enum) => $enum->name === 'PURPLE'))->toBeFalse();

    expect($collection2->contains('GREEN'))->toBeTrue();
    expect($collection2->contains('PURPLE'))->toBeFalse();
    expect($collection2->contains(fn($enum) => $enum === PureEnum::GREEN))->toBeTrue();
    expect($collection2->contains(fn($enum) => $enum->name === 'PURPLE'))->toBeFalse();

    expect($collection3->contains('GREEN'))->toBeTrue();
    expect($collection3->contains('PURPLE'))->toBeFalse();
    expect($collection3->contains(fn($enum) => $enum === PureEnum::GREEN))->toBeTrue();
    expect($collection3->contains(fn($enum) => $enum->name === 'PURPLE'))->toBeFalse();
});

it('will can check if EnumCollection containsAny enum', function ($from, $search, $result) {
    $enumCollection = EnumCollection::from($from);
    $enumCollection2 = EnumCollection::tryFrom($from);
    $enumCollection3 = new EnumCollection($from);

    expect($enumCollection->containsAny($search))->toEqual($result);
    expect($enumCollection->doesntContainAny($search))->toEqual(!$result);

    expect($enumCollection2->containsAny($search))->toEqual($result);
    expect($enumCollection2->doesntContainAny($search))->toEqual(!$result);

    expect($enumCollection3->containsAny($search))->toEqual($result);
    expect($enumCollection3->doesntContainAny($search))->toEqual(!$result);
})->with([
    'pure enum collection search value' => [[PureEnum::GREEN, PureEnum::BLACK], 'GREEN', true],
    'pure enum collection search value2' => [[PureEnum::GREEN, PureEnum::BLACK], ['GREEN'], true],
    'pure enum collection search value3' => [[PureEnum::GREEN, PureEnum::BLACK], ['GREEN', 'RED'], true],
    'pure enum collection search invalid value' => [[PureEnum::GREEN, PureEnum::BLACK], 'PURPLE', false],
    'pure enum collection search invalid value2' => [[PureEnum::GREEN, PureEnum::BLACK], ['PURPLE'], false],
    'pure enum collection search invalid value3' => [[PureEnum::GREEN, PureEnum::BLACK], ['PURPLE', 'RED'], false],
    'pure enum collection search invalid value int' => [[PureEnum::GREEN, PureEnum::BLACK], 1, false],
    'pure enum collection search invalid value int2' => [[PureEnum::GREEN, PureEnum::BLACK], [1], false],
    'pure enum collection search invalid value int3' => [[PureEnum::GREEN, PureEnum::BLACK], [1, 2], false],
    'pure enum collection search enum' => [[PureEnum::GREEN, PureEnum::BLACK], PureEnum::BLACK, true],
    'pure enum collection search enum2' => [[PureEnum::GREEN, PureEnum::BLACK], [PureEnum::BLACK], true],
    'pure enum collection search enum3' => [
        [PureEnum::GREEN, PureEnum::BLACK], [PureEnum::BLACK, PureEnum::BLUE], true
    ],
    'pure enum collection search invalid enum' => [[PureEnum::GREEN, PureEnum::BLACK], PureEnum::YELLOW, false],
    'pure enum collection search invalid enum2' => [[PureEnum::GREEN, PureEnum::BLACK], [PureEnum::YELLOW], false],
    'pure enum collection search invalid enum3' => [
        [PureEnum::GREEN, PureEnum::BLACK], [PureEnum::YELLOW, PureEnum::BLUE], false
    ],
    'pure enum collection search name' => [[PureEnum::GREEN, PureEnum::BLACK], 'BLACK', true],
    'pure enum collection search name2' => [[PureEnum::GREEN, PureEnum::BLACK], ['BLACK'], true],
    'pure enum collection search name3' => [[PureEnum::GREEN, PureEnum::BLACK], ['BLACK', 'BLUE'], true],
    'pure enum collection search invalid name' => [[PureEnum::GREEN, PureEnum::BLACK], 'YELLOW', false],
    'pure enum collection search invalid name2' => [[PureEnum::GREEN, PureEnum::BLACK], ['YELLOW'], false],
    'pure enum collection search invalid name3' => [[PureEnum::GREEN, PureEnum::BLACK], ['YELLOW', 'BLUE'], false],

    'int enum collection search value' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 1, true],
    'int enum collection search value2' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], [1], true],
    'int enum collection search value3' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], [1, 2], true],

    'int enum collection search value string' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], '3', true],
    'int enum collection search value string2' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], ['3'], true],
    'int enum collection search value string3' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], ['3', '4'], true
    ],

    'int enum collection search invalid value' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'A', false],
    'int enum collection search invalid value2' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], ['A'], false],
    'int enum collection search invalid value3' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], ['A', 'B'], false
    ],

    'int enum collection search invalid value2' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 4, false],
    'int enum collection search invalid value22' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], [4], false],
    'int enum collection search invalid value23' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], [4, 5], false],

    'int enum collection search enum' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], IntBackedEnum::PROTECTED, true
    ],
    'int enum collection search enum2' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], [IntBackedEnum::PROTECTED], true
    ],
    'int enum collection search enum3' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], [IntBackedEnum::PROTECTED, IntBackedEnum::PUBLIC], true
    ],

    'int enum collection search invalid enum' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], IntBackedEnum::PUBLIC, false
    ],
    'int enum collection search invalid enum2' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], [IntBackedEnum::PUBLIC], false
    ],
    'int enum collection search invalid enum3' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], [IntBackedEnum::PUBLIC, IntBackedEnum::PUBLIC], false
    ],

    'int enum collection search name' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'PROTECTED', true],
    'int enum collection search name2' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], ['PROTECTED'], true],
    'int enum collection search name3' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], ['PROTECTED', 'PRIVATE'], true
    ],

    'int enum collection search invalid name' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'PUBLIC', false],
    'int enum collection search invalid name2' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], ['PUBLIC'], false
    ],
    'int enum collection search invalid name3' => [
        [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], ['PUBLIC', 'SEMI'], false
    ],

    'string enum collection search value' => [[StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'L', true],
    'string enum collection search value2' => [[StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], ['L'], true],
    'string enum collection search value3' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], ['L', 'M'], true
    ],

    'string enum collection search invalid value' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'LD', false
    ],
    'string enum collection search invalid value2' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], ['LD'], false
    ],
    'string enum collection search invalid value3' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], ['LD', 'MD'], false
    ],

    'string enum collection search invalid value int' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 4, false
    ],
    'string enum collection search invalid value int2' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], [4], false
    ],
    'string enum collection search invalid value int3' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], [4, 5], false
    ],

    'string enum collection search enum' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], StringBackedEnum::EXTRA_LARGE, true
    ],
    'string enum collection search enum2' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], [StringBackedEnum::EXTRA_LARGE], true
    ],
    'string enum collection search enum3' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE],
        [StringBackedEnum::EXTRA_LARGE, StringBackedEnum::LARGE], true
    ],

    'string enum collection search invalid enum' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], StringBackedEnum::SMALL, false
    ],
    'string enum collection search invalid enum2' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], [StringBackedEnum::SMALL], false
    ],
    'string enum collection search invalid enum3' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM],
        false
    ],

    'string enum collection search name' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'EXTRA_LARGE', true
    ],
    'string enum collection search name2' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], ['EXTRA_LARGE'], true
    ],
    'string enum collection search name3' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], ['EXTRA_LARGE', 'LARGE'], true
    ],

    'string enum collection search invalid name' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'SMALL', false
    ],
    'string enum collection search invalid name2' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], ['SMALL'], false
    ],
    'string enum collection search invalid name3' => [
        [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], ['SMALL', 'MEDIUM'], false
    ],
]);

it('supports first', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK]);

    expect($collection->first())->toBe(PureEnum::GREEN);
    expect($collection->first(fn($enum) => $enum->name === 'PURPLE'))->toBeNull();
    expect($collection->first(fn($enum) => $enum->name === 'BLACK'))->toBe(PureEnum::BLACK);

    expect($collection2->first())->toBe(PureEnum::GREEN);
    expect($collection2->first(fn($enum) => $enum->name === 'PURPLE'))->toBeNull();
    expect($collection2->first(fn($enum) => $enum->name === 'BLACK'))->toBe(PureEnum::BLACK);

    expect($collection3->first())->toBe(PureEnum::GREEN);
    expect($collection3->first(fn($enum) => $enum->name === 'PURPLE'))->toBeNull();
    expect($collection3->first(fn($enum) => $enum->name === 'BLACK'))->toBe(PureEnum::BLACK);
});

it('supports map', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK]);

    expect($collection->map(fn($enum) => $enum->name)->toArray())->toBe(['GREEN', 'BLACK']);
    expect($collection2->map(fn($enum) => $enum->name)->toArray())->toBe(['GREEN', 'BLACK']);
    expect($collection3->map(fn($enum) => $enum->name)->toArray())->toBe(['GREEN', 'BLACK']);
});

it('supports mapStrict', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK]);

    expect($collection->mapStrict(fn($enum) => $enum->name)->toArray())->toBe([PureEnum::GREEN, PureEnum::BLACK]);
    expect($collection->mapStrict(fn($enum) => $enum->next())->toArray())->toBe([PureEnum::BLUE, PureEnum::RED]);

    expect($collection2->mapStrict(fn($enum) => $enum->name)->toArray())->toBe([PureEnum::GREEN, PureEnum::BLACK]);
    expect($collection2->mapStrict(fn($enum) => $enum->next())->toArray())->toBe([PureEnum::BLUE, PureEnum::RED]);

    expect($collection3->mapStrict(fn($enum) => $enum->name)->toArray())->toBe([PureEnum::GREEN, PureEnum::BLACK]);
    expect($collection3->mapStrict(fn($enum) => $enum->next())->toArray())->toBe([PureEnum::BLUE, PureEnum::RED]);
});

it('supports map get', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK]);

    expect($collection->map->next()->toArray())->toBe([PureEnum::BLUE, PureEnum::RED]);
    expect($collection->map->name->toArray())->toBe(['GREEN', 'BLACK']);

    expect($collection2->map->next()->toArray())->toBe([PureEnum::BLUE, PureEnum::RED]);
    expect($collection2->map->name->toArray())->toBe(['GREEN', 'BLACK']);

    expect($collection3->map->next()->toArray())->toBe([PureEnum::BLUE, PureEnum::RED]);
    expect($collection3->map->name->toArray())->toBe(['GREEN', 'BLACK']);
});

it('supports diff', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);

    expect($collection->diff([PureEnum::BLACK])->values()->toArray())->toBe([PureEnum::GREEN, PureEnum::RED]);
    expect($collection->diff([PureEnum::BLACK, PureEnum::RED])->toValues())->toBe(['GREEN']);

    expect($collection2->diff([PureEnum::BLACK])->values()->toArray())->toBe([PureEnum::GREEN, PureEnum::RED]);
    expect($collection2->diff([PureEnum::BLACK, PureEnum::RED])->toValues())->toBe(['GREEN']);

    expect($collection3->diff([PureEnum::BLACK])->values()->toArray())->toBe([PureEnum::GREEN, PureEnum::RED]);
    expect($collection3->diff([PureEnum::BLACK, PureEnum::RED])->toValues())->toBe(['GREEN']);
});

it('supports diffUsing', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);

    $fun = fn($item1, $item2) => $item1->name === $item2->name ? 0 : -1;

    expect($collection->diffUsing([PureEnum::BLACK], $fun)->values()->toArray())->toBe([
        PureEnum::GREEN, PureEnum::RED
    ]);
    expect($collection->diffUsing([PureEnum::BLACK, PureEnum::RED], $fun)->toValues())->toBe(['GREEN']);

    expect($collection2->diffUsing([PureEnum::BLACK], $fun)->values()->toArray())->toBe([
        PureEnum::GREEN, PureEnum::RED
    ]);
    expect($collection2->diffUsing([PureEnum::BLACK, PureEnum::RED], $fun)->toValues())->toBe(['GREEN']);

    expect($collection3->diffUsing([PureEnum::BLACK], $fun)->values()->toArray())->toBe([
        PureEnum::GREEN, PureEnum::RED
    ]);
    expect($collection3->diffUsing([PureEnum::BLACK, PureEnum::RED], $fun)->toValues())->toBe(['GREEN']);
});

it('supports diffAssoc', function () {
    $collection = EnumCollection::from([2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED]);

    expect($collection->diffAssoc([3 => PureEnum::BLACK])->toArray())->toBe([2 => PureEnum::GREEN, 4 => PureEnum::RED]);
});

it('supports diffAssocUsing', function () {
    $collection = EnumCollection::from([2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED]);

    $fun = fn($item1, $item2) => $item1 <=> $item2;
    expect($collection->diffAssocUsing([3 => PureEnum::BLACK], $fun)->toArray())->toBe([
        2 => PureEnum::GREEN, 4 => PureEnum::RED
    ]);
});

it('supports diffKeys', function () {
    $collection = EnumCollection::from([2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED]);

    expect($collection->diffKeys([3 => PureEnum::BLACK])->toArray())->toBe([2 => PureEnum::GREEN, 4 => PureEnum::RED]);
});

it('supports diffKeysUsing', function () {
    $collection = EnumCollection::from([2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED]);

    $fun = fn($item1, $item2) => $item1 <=> $item2;
    expect($collection->diffKeysUsing([3 => PureEnum::BLACK], $fun)->toArray())->toBe([
        2 => PureEnum::GREEN, 4 => PureEnum::RED
    ]);
});

it('supports duplicates', function () {
    $collection = EnumCollection::from([
        1 => PureEnum::GREEN, 2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED, 5 => PureEnum::RED
    ]);

    expect($collection->duplicates()->toArray())->toBe([2 => PureEnum::GREEN, 5 => PureEnum::RED]);
});

it('supports except', function () {
    $collection = EnumCollection::from([
        1 => PureEnum::GREEN, 2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED, 5 => PureEnum::RED
    ]);

    expect($collection->except([3, 4])->toArray())->toBe([
        1 => PureEnum::GREEN, 2 => PureEnum::GREEN, 5 => PureEnum::RED
    ]);
});

it('supports filter', function () {
    $collection = EnumCollection::from([
        1 => PureEnum::GREEN, 2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED, 5 => PureEnum::RED
    ]);

    $fun = fn($enum) => strlen($enum->name) < 4;
    expect($collection->filter($fun)->toArray())->toBe([4 => PureEnum::RED, 5 => PureEnum::RED]);
});

it('supports flatten', function () {
    $collection = EnumCollection::from([
        1 => PureEnum::GREEN, 2 => [PureEnum::GREEN, PureEnum::BLACK], 5 => PureEnum::RED
    ]);

    expect($collection->flatten(1)->toArray())->toBe([
        PureEnum::GREEN, PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED
    ]);
});

it('supports intersect', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);

    expect($collection->intersect([PureEnum::GREEN, PureEnum::WHITE])->toArray())->toBe([PureEnum::GREEN]);
});

it('supports intersectUsing', function () {
    $callback = fn($v1, $v2) => $v1->value <=> $v2->value;
    $collection = EnumCollection::from([IntBackedEnum::PROTECTED, IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED]);
    $collection2 = [1, 3, 3, 2, 1];

    expect($collection->intersectUsing($collection2, $callback)->toValues())->toBe([3, 1, 3]);
});

it('supports intersectAssoc', function () {
    $collection = EnumCollection::from([StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::LARGE]);
    $collection2 = [StringBackedEnum::LARGE, StringBackedEnum::MEDIUM, StringBackedEnum::SMALL];
    expect($collection->intersectAssoc($collection2)->toArray())->toBe([1 => StringBackedEnum::MEDIUM]);
});

it('supports intersectByKeys', function () {
    $collection = EnumCollection::from(['a' => StringBackedEnum::SMALL, 'b' => StringBackedEnum::MEDIUM, 'c' => StringBackedEnum::LARGE]);
    $collection2 = ['b' => StringBackedEnum::LARGE, 'c' => StringBackedEnum::MEDIUM, 'f' => StringBackedEnum::SMALL];
    expect($collection->intersectByKeys($collection2)->toArray())->toBe(['b' => StringBackedEnum::MEDIUM, 'c' => StringBackedEnum::LARGE]);
});

it('supports isEmpty', function () {
    $collection = EnumCollection::from([]);
    $collection2 = EnumCollection::from([StringBackedEnum::LARGE]);
    expect($collection->isEmpty())->toBeTrue();
    expect($collection2->isEmpty())->toBeFalse();
});

it('supports containsOneItem', function () {
    $collection = EnumCollection::from([StringBackedEnum::LARGE]);
    expect($collection->containsOneItem())->toBeTrue();

    $collection2 = EnumCollection::from([]);
    expect($collection2->containsOneItem())->toBeFalse();

    $collection3 = EnumCollection::from([StringBackedEnum::LARGE,StringBackedEnum::MEDIUM]);
    expect($collection3->containsOneItem())->toBeFalse();
});

it('supports join', function () {
    $collection = EnumCollection::from([StringBackedEnum::LARGE,StringBackedEnum::SMALL,StringBackedEnum::MEDIUM]);
    expect($collection->join(', ',' and '))->toBe(StringBackedEnum::LARGE->value.', '.StringBackedEnum::SMALL->value.' and '.StringBackedEnum::MEDIUM->value);

    $collection2 = EnumCollection::from(StringBackedEnum::SMALL);
    expect($collection2->join(','))->toBe(StringBackedEnum::SMALL->value);
});

it('supports keys', function () {
    $collection = EnumCollection::from(['a' => StringBackedEnum::SMALL, 'b' => StringBackedEnum::MEDIUM, 'c' => StringBackedEnum::LARGE]);
    expect($collection->keys()->toArray())->toBe(['a','b','c']);
});

it('supports last', function () {
    $collection = EnumCollection::from(['a' => StringBackedEnum::SMALL, 'b' => StringBackedEnum::MEDIUM, 'c' => StringBackedEnum::LARGE]);
    expect($collection->last())->toBe(StringBackedEnum::LARGE);
});

it('not supports pluck', function () {
    $collection = EnumCollection::from([StringBackedEnum::LARGE]);
    expect(fn() => $collection->pluck('name'))->toThrow(MethodNotSupported::class);
});

it('supports mapWithKeys', function () {
    $collection = EnumCollection::from([StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::LARGE]);
    expect($collection->mapWithKeys(fn($enum) => [$enum->name => $enum->value])->toArray())->toBe([
        'SMALL' => StringBackedEnum::SMALL->value,
        'MEDIUM' => StringBackedEnum::MEDIUM->value,
        'LARGE' => StringBackedEnum::LARGE->value
    ]);
});

it('supports mapWithKeysStrinct', function () {
    $collection = EnumCollection::from([StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::LARGE]);
    expect($collection->mapWithKeysStrict(fn($enum) => [$enum->name => $enum->value])->toArray())->toBe([
        'SMALL' => StringBackedEnum::SMALL,
        'MEDIUM' => StringBackedEnum::MEDIUM,
        'LARGE' => StringBackedEnum::LARGE
    ]);
});

it('supports implode', function () {
    $collection = EnumCollection::from([StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::LARGE]);
    $sep = '-';
    expect($collection->implode($sep))->toBe(
        StringBackedEnum::SMALL->value.$sep.StringBackedEnum::MEDIUM->value.$sep.StringBackedEnum::LARGE->value
    );
    $collection2 = EnumCollection::from([
        IntBackedEnum::PRIVATE,
        IntBackedEnum::PROTECTED,
    ]);
    expect($collection2->implode(','))->toBe(
        IntBackedEnum::PRIVATE->value.','.IntBackedEnum::PROTECTED->value
    );

    $collection3 = EnumCollection::from([
        PureEnum::BLACK,
        PureEnum::WHITE,
    ]);
    expect($collection3->implode(','))->toBe(
        'BLACK'.','.'WHITE'
    );
});

it('returns a generic collection when using keys', function () {
    expect(get_class(EnumCollection::of(StringBackedEnum::class)->keys()))
        ->toBe(\Illuminate\Support\Collection::class);
});

it('supports combine', function () {
    $collection = new EnumCollection([
        PureEnum::BLACK,
        PureEnum::WHITE,
    ], PureEnum::class);
    expect($collection->combine(['#0d1117', '#f0f6fc'])->toArray())->toBe([
        'BLACK' => '#0d1117',
        'WHITE' => '#f0f6fc',
    ]);
});

it('supports mapToDictionary', function () {
    $collection = new EnumCollection([
        StringBackedEnum::SMALL,
        StringBackedEnum::MEDIUM,
        StringBackedEnum::LARGE,
    ], StringBackedEnum::class);

    expect($collection->mapToDictionary(fn($enum) => [$enum->name => $enum->value])->toArray())->toBe([
        'SMALL' => ['S'],
        'MEDIUM' => ['M'],
        'LARGE' => ['L'],
    ]);
});

it('supports prepend', function () {
    $collection = new EnumCollection([
        PureEnum::BLACK,
        PureEnum::WHITE,
    ], PureEnum::class);
    expect($collection->prepend(PureEnum::BLUE)[0])->toBe(PureEnum::BLUE);
});


// it('TODO: errore union se non presente in enum')

it('supports forget', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);

    expect($collection->forget(0)->toArray())->toBe([1 => PureEnum::BLACK, 2 => PureEnum::RED]);
});

it('supports merge', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);
    $collection2 = EnumCollection::from([PureEnum::WHITE, PureEnum::RED]);

    expect($collection->merge($collection2)->toArray())->toBe([
        PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED, PureEnum::WHITE, PureEnum::RED
    ]);
    expect($collection->merge($collection2)->unique()->toArray())->toBe([
        PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED, PureEnum::WHITE
    ]);
});

it('forwards call to underlying collection', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK]);

    expect($collection->count())->toEqual(2);

    expect($collection2->count())->toEqual(2);

    expect($collection3->count())->toEqual(2);
});

it('throws on call to non existent', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection->foo();
})->throws(BadMethodCallException::class);

it('throws on call to non existent static', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection::foo();
})->throws(BadMethodCallException::class);
