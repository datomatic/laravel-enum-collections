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
use Illuminate\Support\Collection;

test('enumCollection can accept an EnumCollection on constructor', function () {
    $enumCollection = new EnumCollection([PureEnum::BLACK, PureEnum::RED]);
    $enumCollection2 = new EnumCollection($enumCollection);

    expect($enumCollection2->getEnumClass())->toBe(PureEnum::class);
    expect($enumCollection2->all())->toBe([PureEnum::BLACK, PureEnum::RED]);
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
    expect($enumCollection->all())->toEqual($results);
    expect($enumCollection2)->toBeInstanceOf(EnumCollection::class);
    expect($enumCollection2->all())->toEqual($results);
    expect($enumCollection3)->toBeInstanceOf(EnumCollection::class);
    expect($enumCollection3->all())->toEqual($results);
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
        expect($enumCollection->all())->toEqual($results);

        expect($enumCollection2)->toBeInstanceOf(EnumCollection::class);
        expect($enumCollection2->all())->toEqual($results);

        expect($enumCollection3)->toBeInstanceOf(EnumCollection::class);
        expect($enumCollection3->all())->toEqual($results);
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

    'int enum collection search invalid string value' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'A', false],
    'int enum collection search invalid string value2' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], ['A'], false],
    'int enum collection search invalid string value3' => [
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

    expect($collection->map(fn($enum) => $enum->name)->all())->toBe(['GREEN', 'BLACK']);
    expect($collection2->map(fn($enum) => $enum->name)->all())->toBe(['GREEN', 'BLACK']);
    expect($collection3->map(fn($enum) => $enum->name)->all())->toBe(['GREEN', 'BLACK']);
    expect($collection3->map(fn($enum) => $enum->name))->toBeInstanceOf(Collection::class);
});

it('supports mapStrict', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK]);

    expect($collection->mapStrict(fn($enum) => $enum->name)->all())->toBe([PureEnum::GREEN, PureEnum::BLACK]);
    expect($collection->mapStrict(fn($enum) => $enum->next())->all())->toBe([PureEnum::BLUE, PureEnum::RED]);

    expect($collection2->mapStrict(fn($enum) => $enum->name)->all())->toBe([PureEnum::GREEN, PureEnum::BLACK]);
    expect($collection2->mapStrict(fn($enum) => $enum->next())->all())->toBe([PureEnum::BLUE, PureEnum::RED]);

    expect($collection3->mapStrict(fn($enum) => $enum->name)->all())->toBe([PureEnum::GREEN, PureEnum::BLACK]);
    expect($collection3->mapStrict(fn($enum) => $enum->next())->all())->toBe([PureEnum::BLUE, PureEnum::RED]);
    expect($collection3->mapStrict(fn($enum) => $enum->next()))->toBeInstanceOf(EnumCollection::class);
});

it('supports map get', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK]);

    expect($collection->map->next()->all())->toBe([PureEnum::BLUE, PureEnum::RED]);
    expect($collection->map->name->all())->toBe(['GREEN', 'BLACK']);

    expect($collection2->map->next()->all())->toBe([PureEnum::BLUE, PureEnum::RED]);
    expect($collection2->map->name->all())->toBe(['GREEN', 'BLACK']);

    expect($collection3->map->next()->all())->toBe([PureEnum::BLUE, PureEnum::RED]);
    expect($collection3->map->name->all())->toBe(['GREEN', 'BLACK']);
    expect($collection3->map->name)->toBeInstanceOf(Collection::class);
});

it('supports diff', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);

    expect($collection->diff([PureEnum::BLACK])->values()->all())->toBe([PureEnum::GREEN, PureEnum::RED]);
    expect($collection->diff([PureEnum::BLACK, PureEnum::RED])->toValues())->toBe(['GREEN']);

    expect($collection2->diff([PureEnum::BLACK])->values()->all())->toBe([PureEnum::GREEN, PureEnum::RED]);
    expect($collection2->diff([PureEnum::BLACK, PureEnum::RED])->toValues())->toBe(['GREEN']);

    expect($collection3->diff([PureEnum::BLACK])->values()->all())->toBe([PureEnum::GREEN, PureEnum::RED]);
    expect($collection3->diff([PureEnum::BLACK, PureEnum::RED])->toValues())->toBe(['GREEN']);
    expect($collection3->diff([PureEnum::BLACK, PureEnum::RED]))->toBeInstanceOf(EnumCollection::class);
});

it('supports diffUsing', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);
    $collection2 = EnumCollection::tryFrom([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);
    $collection3 = new EnumCollection([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);

    $fun = fn($item1, $item2) => $item1->name === $item2->name ? 0 : -1;

    expect($collection->diffUsing([PureEnum::BLACK], $fun)->values()->all())->toBe([
        PureEnum::GREEN, PureEnum::RED
    ]);
    expect($collection->diffUsing([PureEnum::BLACK, PureEnum::RED], $fun)->toValues())->toBe(['GREEN']);

    expect($collection2->diffUsing([PureEnum::BLACK], $fun)->values()->all())->toBe([
        PureEnum::GREEN, PureEnum::RED
    ]);
    expect($collection2->diffUsing([PureEnum::BLACK, PureEnum::RED], $fun)->toValues())->toBe(['GREEN']);

    expect($collection3->diffUsing([PureEnum::BLACK], $fun)->values()->all())->toBe([
        PureEnum::GREEN, PureEnum::RED
    ]);
    expect($collection3->diffUsing([PureEnum::BLACK, PureEnum::RED], $fun)->toValues())->toBe(['GREEN']);
    expect($collection3->diffUsing([PureEnum::BLACK, PureEnum::RED], $fun))->toBeInstanceOf(EnumCollection::class);
});

it('supports diffAssoc', function () {
    $collection = EnumCollection::from([2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED]);

    expect($collection->diffAssoc([3 => PureEnum::BLACK])->all())->toBe([2 => PureEnum::GREEN, 4 => PureEnum::RED]);
    expect($collection->diffAssoc([3 => PureEnum::BLACK]))->toBeInstanceOf(EnumCollection::class);
});

it('supports diffAssocUsing', function () {
    $collection = EnumCollection::from([2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED]);

    $fun = fn($item1, $item2) => $item1 <=> $item2;
    expect($collection->diffAssocUsing([3 => PureEnum::BLACK], $fun)->all())->toBe([
        2 => PureEnum::GREEN, 4 => PureEnum::RED
    ]);
});

it('supports diffKeys', function () {
    $collection = EnumCollection::from([2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED]);

    expect($collection->diffKeys([3 => PureEnum::BLACK])->all())->toBe([2 => PureEnum::GREEN, 4 => PureEnum::RED]);
    expect($collection->diffKeys([3 => PureEnum::BLACK]))->toBeInstanceOf(EnumCollection::class);
});

it('supports diffKeysUsing', function () {
    $collection = EnumCollection::from([2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED]);

    $fun = fn($item1, $item2) => $item1 <=> $item2;
    expect($collection->diffKeysUsing([3 => PureEnum::BLACK], $fun)->all())->toBe([
        2 => PureEnum::GREEN, 4 => PureEnum::RED
    ]);
});

it('supports duplicates', function () {
    $collection = EnumCollection::from([
        1 => PureEnum::GREEN, 2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED, 5 => PureEnum::RED
    ]);

    expect($collection->duplicates()->all())->toBe([2 => PureEnum::GREEN, 5 => PureEnum::RED]);
    expect($collection->duplicates())->toBeInstanceOf(EnumCollection::class);
});

it('supports except', function () {
    $collection = EnumCollection::from([
        1 => PureEnum::GREEN, 2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED, 5 => PureEnum::RED
    ]);

    expect($collection->except([3, 4])->all())->toBe([
        1 => PureEnum::GREEN, 2 => PureEnum::GREEN, 5 => PureEnum::RED
    ]);
    expect($collection->except([3, 4]))->toBeInstanceOf(EnumCollection::class);
});

it('supports filter', function () {
    $collection = EnumCollection::from([
        1 => PureEnum::GREEN, 2 => PureEnum::GREEN, 3 => PureEnum::BLACK, 4 => PureEnum::RED, 5 => PureEnum::RED
    ]);

    $fun = fn($enum) => strlen($enum->name) < 4;
    expect($collection->filter($fun)->all())->toBe([4 => PureEnum::RED, 5 => PureEnum::RED]);
    expect($collection->filter($fun))->toBeInstanceOf(EnumCollection::class);
});

it('supports flatten', function () {
    $collection = EnumCollection::from([
        1 => PureEnum::GREEN, 2 => [PureEnum::GREEN, PureEnum::BLACK], 5 => PureEnum::RED
    ]);

    expect($collection->flatten(1)->all())->toBe([
        PureEnum::GREEN, PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED
    ]);
    expect($collection->flatten(1))->toBeInstanceOf(EnumCollection::class);
});

it('supports intersect', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);

    expect($collection->intersect([PureEnum::GREEN, PureEnum::WHITE])->all())->toBe([PureEnum::GREEN]);
    expect($collection->intersect([PureEnum::GREEN, PureEnum::WHITE]))->toBeInstanceOf(EnumCollection::class);
});

it('supports intersectUsing', function () {
    $callback = fn($v1, $v2) => $v1->value <=> $v2->value;
    $collection = EnumCollection::from([IntBackedEnum::PROTECTED, IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED]);
    $collection2 = [1, 3, 3, 2, 1];

    expect($collection->intersectUsing($collection2, $callback)->toValues())->toBe([3, 1, 3]);
    $collection = EnumCollection::from([IntBackedEnum::PROTECTED, IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED]);
    expect($collection->intersectUsing($collection2, $callback))->toBeInstanceOf(EnumCollection::class);
});

it('supports intersectAssoc', function () {
    $collection = EnumCollection::from([StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::LARGE]);
    $collection2 = [StringBackedEnum::LARGE, StringBackedEnum::MEDIUM, StringBackedEnum::SMALL];
    expect($collection->intersectAssoc($collection2)->all())->toBe([1 => StringBackedEnum::MEDIUM]);
    expect($collection->intersectAssoc($collection2))->toBeInstanceOf(EnumCollection::class);
});

it('supports intersectByKeys', function () {
    $collection = EnumCollection::from([
        'a' => StringBackedEnum::SMALL, 'b' => StringBackedEnum::MEDIUM, 'c' => StringBackedEnum::LARGE
    ]);
    $collection2 = ['b' => StringBackedEnum::LARGE, 'c' => StringBackedEnum::MEDIUM, 'f' => StringBackedEnum::SMALL];
    expect($collection->intersectByKeys($collection2)->all())->toBe([
        'b' => StringBackedEnum::MEDIUM, 'c' => StringBackedEnum::LARGE
    ]);
    expect($collection->intersectByKeys($collection2))->toBeInstanceOf(EnumCollection::class);
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

    $collection3 = EnumCollection::from([StringBackedEnum::LARGE, StringBackedEnum::MEDIUM]);
    expect($collection3->containsOneItem())->toBeFalse();
});

it('supports join', function () {
    $collection = EnumCollection::from([StringBackedEnum::LARGE, StringBackedEnum::SMALL, StringBackedEnum::MEDIUM]);
    expect($collection->join(', ',
        ' and '))->toBe(StringBackedEnum::LARGE->value.', '.StringBackedEnum::SMALL->value.' and '.StringBackedEnum::MEDIUM->value);

    $collection2 = EnumCollection::from(StringBackedEnum::SMALL);
    expect($collection2->join(','))->toBe(StringBackedEnum::SMALL->value);
});

it('supports keys', function () {
    $collection = EnumCollection::from([
        'a' => StringBackedEnum::SMALL, 'b' => StringBackedEnum::MEDIUM, 'c' => StringBackedEnum::LARGE
    ]);
    expect($collection->keys()->all())->toBe(['a', 'b', 'c']);

    expect(EnumCollection::of(StringBackedEnum::class)->keys())
        ->toBeInstanceOf(Collection::class);
});

it('supports last', function () {
    $collection = EnumCollection::from([
        'a' => StringBackedEnum::SMALL, 'b' => StringBackedEnum::MEDIUM, 'c' => StringBackedEnum::LARGE
    ]);
    expect($collection->last())->toBe(StringBackedEnum::LARGE);
});

it('supports mapWithKeys', function () {
    $collection = EnumCollection::from([StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::LARGE]);
    expect($collection->mapWithKeys(fn($enum) => [$enum->name => $enum->value])->all())->toBe([
        'SMALL' => StringBackedEnum::SMALL->value,
        'MEDIUM' => StringBackedEnum::MEDIUM->value,
        'LARGE' => StringBackedEnum::LARGE->value
    ]);
    expect($collection->mapWithKeys(fn($enum) => [$enum->name => $enum->value]))->toBeInstanceOf(Collection::class);
});

it('supports mapWithKeysStrinct', function () {
    $collection = EnumCollection::from([StringBackedEnum::SMALL, StringBackedEnum::MEDIUM, StringBackedEnum::LARGE]);
    expect($collection->mapWithKeysStrict(fn($enum) => [$enum->name => $enum->value])->all())->toBe([
        'SMALL' => StringBackedEnum::SMALL,
        'MEDIUM' => StringBackedEnum::MEDIUM,
        'LARGE' => StringBackedEnum::LARGE
    ]);
    expect($collection->mapWithKeysStrict(fn($enum) => [$enum->name => $enum->value]))->toBeInstanceOf(EnumCollection::class);
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

it('supports combine', function () {
    $collection = new EnumCollection([
        PureEnum::BLACK,
        PureEnum::WHITE,
    ], PureEnum::class);
    expect($collection->combine(['#0d1117', '#f0f6fc'])->all())->toBe([
        'BLACK' => '#0d1117',
        'WHITE' => '#f0f6fc',
    ]);
    $collection = new EnumCollection([
        PureEnum::BLACK,
        PureEnum::WHITE,
    ], PureEnum::class);
    expect($collection->combine(['#0d1117', '#f0f6fc']))->toBeInstanceOf(Collection::class);
});

it('supports mapToDictionary', function () {
    $collection = new EnumCollection([
        StringBackedEnum::SMALL,
        StringBackedEnum::MEDIUM,
        StringBackedEnum::LARGE,
    ], StringBackedEnum::class);

    expect($collection->mapToDictionary(fn($enum) => [$enum->name => $enum->value])->all())->toBe([
        'SMALL' => ['S'],
        'MEDIUM' => ['M'],
        'LARGE' => ['L'],
    ]);
    expect($collection->mapToDictionary(fn($enum) => [$enum->name => $enum->value]))->toBeInstanceOf(Collection::class);
});

it('supports forget', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);

    expect($collection->forget(0)->all())->toBe([1 => PureEnum::BLACK, 2 => PureEnum::RED]);
    expect($collection->forget(0))->toBeInstanceOf(EnumCollection::class);
});

it('supports merge', function () {
    $collection = EnumCollection::from([PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED]);
    $collection2 = EnumCollection::from([PureEnum::WHITE, PureEnum::RED]);

    expect($collection->merge($collection2)->all())->toBe([
        PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED, PureEnum::WHITE, PureEnum::RED
    ]);
    expect($collection->merge($collection2)->unique()->all())->toBe([
        PureEnum::GREEN, PureEnum::BLACK, PureEnum::RED, PureEnum::WHITE
    ]);
    expect($collection->merge($collection2))->toBeInstanceOf(EnumCollection::class);
});

it('supports multiply', function () {
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED]);

    expect($collection->multiply(2)->all())->toBe([
        PureEnum::WHITE, PureEnum::RED, PureEnum::WHITE, PureEnum::RED
    ]);
    expect($collection->multiply(2))->toBeInstanceOf(EnumCollection::class);
});

it('supports union', function () {
    $collection = new EnumCollection([
        'a' => PureEnum::BLACK,
        'b' => PureEnum::WHITE,
    ], PureEnum::class);
    expect($collection->union([
        'c' => PureEnum::YELLOW,
        'b' => PureEnum::WHITE,
    ])->all())->toBe([
        'a' => PureEnum::BLACK,
        'b' => PureEnum::WHITE,
        'c' => PureEnum::YELLOW,
    ]);
    expect($collection->union([
        'c' => PureEnum::YELLOW,
        'b' => PureEnum::WHITE,
    ]))->toBeInstanceOf(EnumCollection::class);
});


it('supports nth', function () {
    $collection = EnumCollection::from([
        PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW, PureEnum::BLUE, PureEnum::BLACK
    ]);

    expect($collection->nth(2, 1)->all())->toBe([
        PureEnum::RED, PureEnum::BLUE
    ]);
    expect($collection->nth(2, 1))->toBeInstanceOf(EnumCollection::class);
});

it('supports only', function () {
    $collection = EnumCollection::from([
        PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW, PureEnum::BLUE, PureEnum::BLACK
    ]);

    expect($collection->only([2, 1])->all())->toBe([
        1 => PureEnum::RED, 2 => PureEnum::YELLOW
    ]);
    expect($collection->only([2, 1]))->toBeInstanceOf(EnumCollection::class);
});

it('supports pop', function () {
    $collection = EnumCollection::from([
        PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW, PureEnum::BLUE, PureEnum::BLACK
    ]);
    expect($collection->pop(2)->all())->toBe([PureEnum::BLACK, PureEnum::BLUE]);

    $collection = EnumCollection::from([
        PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW, PureEnum::BLUE, PureEnum::BLACK
    ]);
    expect($collection->pop())->toBe(PureEnum::BLACK);
});

it('supports prepend', function () {
    $collection = EnumCollection::from([PureEnum::YELLOW, PureEnum::BLUE]);

    expect($collection->prepend(PureEnum::WHITE)->all())->toBe([PureEnum::WHITE, PureEnum::YELLOW, PureEnum::BLUE]);
    expect($collection->prepend('WHITE')->all())->toBe([
        PureEnum::WHITE, PureEnum::WHITE, PureEnum::YELLOW, PureEnum::BLUE
    ]);
    expect($collection->prepend(PureEnum::WHITE))->toBeInstanceOf(EnumCollection::class);
});

it('supports push', function () {
    $collection = EnumCollection::from([PureEnum::YELLOW, PureEnum::BLUE]);

    expect($collection->push(PureEnum::WHITE)->all())->toBe([PureEnum::YELLOW, PureEnum::BLUE, PureEnum::WHITE]);
    expect($collection->push('WHITE')->all())->toBe([
        PureEnum::YELLOW, PureEnum::BLUE, PureEnum::WHITE, PureEnum::WHITE
    ]);
    expect($collection->push(PureEnum::WHITE))->toBeInstanceOf(EnumCollection::class);
});

it('supports unshift', function () {
    $collection = EnumCollection::from([PureEnum::YELLOW, PureEnum::BLUE]);

    expect($collection->unshift(PureEnum::WHITE)->all())->toBe([PureEnum::WHITE, PureEnum::YELLOW, PureEnum::BLUE]);
    expect($collection->unshift('WHITE')->all())->toBe([
        PureEnum::WHITE, PureEnum::WHITE, PureEnum::YELLOW, PureEnum::BLUE
    ]);
    expect($collection->unshift('WHITE'))->toBeInstanceOf(EnumCollection::class);
});

it('supports concat', function () {
    $collection = EnumCollection::from([PureEnum::YELLOW, PureEnum::BLUE]);

    expect($collection->concat([PureEnum::WHITE])->all())->toBe([
        PureEnum::YELLOW, PureEnum::BLUE, PureEnum::WHITE
    ]);
    expect($collection->concat(['name' => 'WHITE'])->all())->toBe([
        PureEnum::YELLOW, PureEnum::BLUE, PureEnum::WHITE
    ]);
    expect($collection->concat(['name' => 'WHITE']))->toBeInstanceOf(EnumCollection::class);
});

it('supports pull', function () {
    $collection = EnumCollection::from([PureEnum::YELLOW, PureEnum::BLUE]);

    expect($collection->push(PureEnum::WHITE)->all())->toBe([PureEnum::YELLOW, PureEnum::BLUE, PureEnum::WHITE]);
    expect($collection->push('WHITE')->all())->toBe([
        PureEnum::YELLOW, PureEnum::BLUE, PureEnum::WHITE, PureEnum::WHITE
    ]);
    expect($collection->push(PureEnum::WHITE))->toBeInstanceOf(EnumCollection::class);
});

it('supports put', function () {
    $collection = EnumCollection::from([PureEnum::YELLOW, PureEnum::BLUE]);

    expect($collection->put(2, PureEnum::WHITE)->all())->toBe([PureEnum::YELLOW, PureEnum::BLUE, PureEnum::WHITE]);
    expect($collection->put(2, 'WHITE')->all())->toBe([PureEnum::YELLOW, PureEnum::BLUE, PureEnum::WHITE]);
    expect($collection->put(2, 'WHITE'))->toBeInstanceOf(EnumCollection::class);
});

it('supports random', function () {
    $collection = EnumCollection::from([
        PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW, PureEnum::BLUE, PureEnum::BLACK
    ]);

    expect($collection->random())->toBeInstanceOf(PureEnum::class);
    expect($collection->random(2)->count())->toBe(2);
    expect($collection->random(2))->toBeInstanceOf(EnumCollection::class);
});

it('supports replace', function () {
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);
    expect($collection->replace([1 => PureEnum::BLACK]))->toBeInstanceOf(EnumCollection::class);

    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);
    expect($collection->replace([1 => PureEnum::BLACK])->all())->toBe([
        PureEnum::WHITE, PureEnum::BLACK, PureEnum::YELLOW
    ]);
});

it('supports reverse', function () {
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);
    expect($collection->reverse())->toBeInstanceOf(EnumCollection::class);

    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);

    expect($collection->reverse()->all())->toBe([2 => PureEnum::YELLOW, 1 => PureEnum::RED, 0 => PureEnum::WHITE]);
});

it('supports search', function () {
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);

    expect($collection->search(PureEnum::WHITE))->toBe(0);
    expect($collection->search('WHITE'))->toBe(0);
});

it('supports before', function () {
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);

    expect($collection->before(PureEnum::RED,true))->toBe(PureEnum::WHITE);
    expect($collection->before('YELLOW'))->toBe(PureEnum::RED);
});

it('supports after', function () {
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);

    expect($collection->after(PureEnum::RED,true))->toBe(PureEnum::YELLOW);
    expect($collection->after('WHITE'))->toBe(PureEnum::RED);
});

it('supports shift', function () {
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);
    expect($collection->shift())->toBeInstanceOf(EnumCollection::class);
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);
    expect($collection->shift()->all())->toBe([PureEnum::WHITE]);
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);
    expect($collection->shift(2)->all())->toBe([PureEnum::WHITE, PureEnum::RED]);
});

it('supports shuffle', function () {
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW])->shuffle();

    expect($collection)->toBeInstanceOf(EnumCollection::class);
    expect($collection->count())->toBe(3);
});

it('supports skip', function () {
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);

    expect($collection->skip(2)->all())->toBe([2 => PureEnum::YELLOW]);
});

it('supports skipUntil', function () {
    $collection = EnumCollection::from([PureEnum::WHITE, PureEnum::RED, PureEnum::YELLOW]);
    $collection = $collection->skipUntil(fn (PureEnum $item) => $item->name === 'RED');

    expect($collection)->toBeInstanceOf(EnumCollection::class);
    expect($collection->all())
        ->toBe([1 => PureEnum::RED,2 => PureEnum::YELLOW]);


    $collection2 = $collection->skipUntil(fn (PureEnum $item) => $item->name === 'ddd');
    expect($collection2)->toBeInstanceOf(EnumCollection::class);
    expect($collection2->isEmpty())->toBeTrue();
});


it('not supports pluck', function () {
    $collection = EnumCollection::from([StringBackedEnum::LARGE]);
    expect(fn() => $collection->pluck('name'))->toThrow(MethodNotSupported::class);
});

it('not supports mergeRecursive', function () {
    $collection = EnumCollection::from([StringBackedEnum::LARGE]);
    expect(fn() => $collection->mergeRecursive([]))->toThrow(MethodNotSupported::class);
});
it('not supports select', function () {
    $collection = EnumCollection::from([StringBackedEnum::LARGE]);
    expect(fn() => $collection->select('name'))->toThrow(MethodNotSupported::class);
});
it('not supports replaceRecursive', function () {
    $collection = EnumCollection::from([StringBackedEnum::LARGE]);
    expect(fn() => $collection->replaceRecursive(['name']))->toThrow(MethodNotSupported::class);
});
it('not supports sliding', function () {
    $collection = EnumCollection::from([StringBackedEnum::LARGE]);
    expect(fn() => $collection->sliding())->toThrow(MethodNotSupported::class);
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
