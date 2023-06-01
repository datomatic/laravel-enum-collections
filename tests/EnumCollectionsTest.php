<?php

use Datomatic\EnumCollections\EnumCollection;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\IntBackedEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\PureEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\StringBackedEnum;
use Datomatic\EnumCollections\Tests\TestSupport\TestModel;

beforeEach(function () {
    $this->testModel = new TestModel();
});

it('will return an EnumCollection setting enum collection fields with pure enum', function ($from, array $results) {
    $this->testModel->colors = $from;
    $this->testModel->save();

    $model = TestModel::find($this->testModel->id);

    expect($model->colors)->toBeInstanceOf(EnumCollection::class);

    expect($model->colors->toArray())->toEqual($results);
})->with([
    'enum single' => [PureEnum::BLACK, [PureEnum::BLACK]],
    'string case single' => ['BLACK', [PureEnum::BLACK]],
    'enum array' => [[PureEnum::BLACK, PureEnum::GREEN], [PureEnum::BLACK, PureEnum::GREEN]],
    'string case array' => [['BLACK', 'GREEN'], [PureEnum::BLACK, PureEnum::GREEN]],
]);

it('will return an EnumCollection setting enum collection fields with IntBackedEnum', function ($from, array $results) {
    $this->testModel->visibilities = $from;
    $this->testModel->save();

    $model = TestModel::find($this->testModel->id);

    expect($model->visibilities)->toBeInstanceOf(EnumCollection::class);

    expect($model->visibilities->toArray())->toEqual($results);
})->with([
    'enum single' => [IntBackedEnum::PRIVATE, [IntBackedEnum::PRIVATE]],
    'string case single' => ['PRIVATE', [IntBackedEnum::PRIVATE]],
    'int value single' => [1, [IntBackedEnum::PRIVATE]],
    'string value single' => ['1', [IntBackedEnum::PRIVATE]],
    'enum array' => [[IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC], [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
    'string case array' => [['PRIVATE', 'PUBLIC'], [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
    'int value array' => [[1, 2], [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
    'string value array' => [['1', '2'], [IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]],
]);

it('will return an EnumCollection setting enum collection fields with StringBackedEnum', function ($from, array $results) {
    $this->testModel->sizes = $from;
    $this->testModel->save();

    $model = TestModel::find($this->testModel->id);

    expect($model->sizes)->toBeInstanceOf(EnumCollection::class);

    expect($model->sizes->toArray())->toEqual($results);
})->with([
    'enum single' => [StringBackedEnum::SMALL, [StringBackedEnum::SMALL]],
    'string case single' => ['SMALL', [StringBackedEnum::SMALL]],
    'string value single' => ['S', [StringBackedEnum::SMALL]],
    'missing string value single' => ['A', []],
    'enum array' => [[StringBackedEnum::SMALL, StringBackedEnum::MEDIUM], [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM]],
    'string case array' => [['SMALL', 'MEDIUM'], [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM]],
    'string value array' => [['S', 'M'], [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM]],
    'missing string value array' => [['A', 'S'], [StringBackedEnum::SMALL]],
    'missing string value array2' => [['S', 'SS'], [StringBackedEnum::SMALL]],
]);

it('will can check if enum collection contains enum', function ($field, $from, $search, $result) {
    $this->testModel->$field = $from;
    $this->testModel->save();

    $model = TestModel::find($this->testModel->id);
    expect($model->$field->contains($search))->toEqual($result);
})->with([
    'pure enum collection search value' => ['colors', [PureEnum::GREEN, PureEnum::BLACK], 'GREEN', true],
    'pure enum collection search invalid value' => ['colors', [PureEnum::GREEN, PureEnum::BLACK], 'PURPLE', false],
    'pure enum collection search invalid value int' => ['colors', [PureEnum::GREEN, PureEnum::BLACK], 1, false],
    'pure enum collection search enum' => ['colors', [PureEnum::GREEN, PureEnum::BLACK], PureEnum::BLACK, true],
    'pure enum collection search invalid enum' => ['colors', [PureEnum::GREEN, PureEnum::BLACK], PureEnum::YELLOW, false],
    'pure enum collection search name' => ['colors', [PureEnum::GREEN, PureEnum::BLACK], 'BLACK', true],
    'pure enum collection search invalid name' => ['colors', [PureEnum::GREEN, PureEnum::BLACK], 'YELLOW', false],

    'int enum collection search value' => ['visibilities', [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 1, true],
    'int enum collection search value string' => ['visibilities', [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], '3', true],
    'int enum collection search invalid value' => ['visibilities', [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'A', false],
    'int enum collection search invalid value2' => ['visibilities', [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 4, false],
    'int enum collection search enum' => ['visibilities', [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], IntBackedEnum::PROTECTED, true],
    'int enum collection search invalid enum' => ['visibilities', [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], IntBackedEnum::PUBLIC, false],
    'int enum collection search name' => ['visibilities', [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'PROTECTED', true],
    'int enum collection search invalid name' => ['visibilities', [IntBackedEnum::PRIVATE, IntBackedEnum::PROTECTED], 'PUBLIC', false],

    'string enum collection search value' => ['sizes', [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'L', true],
    'string enum collection search invalid value' => ['sizes', [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'LD', false],
    'string enum collection search invalid value int' => ['sizes', [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 4, false],
    'string enum collection search enum' => ['sizes', [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], StringBackedEnum::EXTRA_LARGE, true],
    'string enum collection search invalid enum' => ['sizes', [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], StringBackedEnum::SMALL, false],
    'string enum collection search name' => ['sizes', [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'EXTRA_LARGE', true],
    'string enum collection search invalid name' => ['sizes', [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'SMALL', false],
]);

it('will can query model with enum collection', function () {
    \DB::table('test_models')->delete();

    $this->testModel->colors = [PureEnum::YELLOW, PureEnum::GREEN];
    $this->testModel->visibilities = [IntBackedEnum::PUBLIC];
    $this->testModel->sizes = [StringBackedEnum::SMALL, StringBackedEnum::EXTRA_LARGE, StringBackedEnum::MEDIUM];
    $this->testModel->save();

    $this->testModel = new TestModel();
    $this->testModel->colors = [PureEnum::BLACK, PureEnum::BLUE, PureEnum::YELLOW];
    $this->testModel->visibilities = [IntBackedEnum::PROTECTED, IntBackedEnum::PRIVATE];
    $this->testModel->sizes = [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM];
    $this->testModel->save();

    expect(TestModel::whereEnumCollectionContains('colors', PureEnum::YELLOW)->count())->toEqual(2);
    expect(TestModel::whereEnumCollectionContains('colors', PureEnum::BLACK)->count())->toEqual(1);
    expect(TestModel::whereEnumCollectionContains('colors', PureEnum::RED)->count())->toEqual(0);

    expect(
        TestModel::whereEnumCollectionContains('colors', PureEnum::BLACK)
            ->whereEnumCollectionContains('colors', PureEnum::BLUE)->count()
    )->toEqual(1);

    expect(
        TestModel::whereEnumCollectionContains('colors', PureEnum::BLACK)
            ->whereEnumCollectionContains('colors', PureEnum::BLUE)->count()
    )->toEqual(1);

    expect(
        TestModel::whereEnumCollectionContains('colors', PureEnum::RED)
            ->orWhereEnumCollectionContains('sizes', StringBackedEnum::SMALL)->count()
    )->toEqual(2);
    expect(
        TestModel::orWhereEnumCollectionContains('colors', PureEnum::RED)
            ->orWhereEnumCollectionContains('sizes', StringBackedEnum::SMALL)->count()
    )->toEqual(2);
});
