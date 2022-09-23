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
    'string enum collection search value' => ['sizes', [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'L', true],
    'string enum collection search invalid value' => ['sizes', [StringBackedEnum::LARGE, StringBackedEnum::EXTRA_LARGE], 'LD', false],
]);
