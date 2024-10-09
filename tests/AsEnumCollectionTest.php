<?php

declare(strict_types=1);

use Datomatic\EnumCollections\EnumCollection;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\IntBackedEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\LaravelEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\PureEnum;
use Datomatic\EnumCollections\Tests\TestSupport\Enums\StringBackedEnum;
use Datomatic\EnumCollections\Tests\TestSupport\TestModel;
use Illuminate\Database\Eloquent\Model;

beforeEach(function () {
    $this->testModel = new TestModel;
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

it('will return an EnumCollection setting enum collection fields with LaravelEnum', function ($from, array $results) {
    $this->testModel->permissions = $from;
    $this->testModel->save();

    $model = TestModel::find($this->testModel->id);

    expect($model->permissions)->toBeInstanceOf(EnumCollection::class);
    expect($model->permissions->toArray())->toEqual($results);
})->with([
    'enum single' => [LaravelEnum::PRIVATE, [LaravelEnum::PRIVATE]],
    'string case single' => ['PRIVATE', [LaravelEnum::PRIVATE]],
    'int value single' => [1, [LaravelEnum::PRIVATE]],
    'string value single' => ['1', [LaravelEnum::PRIVATE]],
    'enum array' => [[LaravelEnum::PRIVATE, LaravelEnum::PUBLIC], [LaravelEnum::PRIVATE, LaravelEnum::PUBLIC]],
    'string case array' => [['PRIVATE', 'PUBLIC'], [LaravelEnum::PRIVATE, LaravelEnum::PUBLIC]],
    'int value array' => [[1, 2], [LaravelEnum::PRIVATE, LaravelEnum::PUBLIC]],
    'string value array' => [['1', '2'], [LaravelEnum::PRIVATE, LaravelEnum::PUBLIC]],
]);

it('will return an EnumCollection setting enum collection fields with StringBackedEnum', function ($from, array $results) {
    $this->testModel->sizes = $from;
    $this->testModel->save();

    $model = TestModel::find($this->testModel->id);

    expect($model->sizes)->toBeInstanceOf(EnumCollection::class);
    expect($model->sizes->values()->toArray())->toEqual($results);
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
    expect($model->$field->doesntContain($search))->toEqual(! $result);
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

    'int laravel enum collection search value' => ['permissions', [LaravelEnum::PRIVATE, LaravelEnum::PROTECTED], 1, true],
    'int laravel enum collection search value string' => ['permissions', [LaravelEnum::PRIVATE, LaravelEnum::PROTECTED], '3', true],
    'int laravel enum collection search invalid value' => ['permissions', [LaravelEnum::PRIVATE, LaravelEnum::PROTECTED], 'A', false],
    'int laravel enum collection search invalid value2' => ['permissions', [LaravelEnum::PRIVATE, LaravelEnum::PROTECTED], 4, false],
    'int laravel enum collection search enum' => ['permissions', [LaravelEnum::PRIVATE, LaravelEnum::PROTECTED], LaravelEnum::PROTECTED, true],
    'int laravel enum collection search invalid enum' => ['permissions', [LaravelEnum::PRIVATE, LaravelEnum::PROTECTED], LaravelEnum::PUBLIC, false],
    'int laravel enum collection search name' => ['permissions', [LaravelEnum::PRIVATE, LaravelEnum::PROTECTED], 'PROTECTED', true],
    'int laravel enum collection search invalid name' => ['permissions', [LaravelEnum::PRIVATE, LaravelEnum::PROTECTED], 'PUBLIC', false],

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
    $this->testModel->json = [PureEnum::YELLOW->name, PureEnum::GREEN->name];
    $this->testModel->visibilities = [IntBackedEnum::PUBLIC];
    $this->testModel->permissions = [IntBackedEnum::PUBLIC];
    $this->testModel->sizes = [StringBackedEnum::SMALL, StringBackedEnum::EXTRA_LARGE, StringBackedEnum::MEDIUM];
    $this->testModel->save();

    $this->testModel = new TestModel;
    $this->testModel->colors = [PureEnum::BLACK, PureEnum::BLUE, PureEnum::YELLOW];
    $this->testModel->json = [PureEnum::BLACK->name, PureEnum::BLUE->name, PureEnum::YELLOW->name];
    $this->testModel->visibilities = [IntBackedEnum::PROTECTED, IntBackedEnum::PRIVATE];
    $this->testModel->permissions = [IntBackedEnum::PROTECTED, IntBackedEnum::PRIVATE];
    $this->testModel->sizes = [StringBackedEnum::SMALL, StringBackedEnum::MEDIUM];
    $this->testModel->save();

    expect(TestModel::whereContains('colors', PureEnum::YELLOW)->count())->toEqual(2);
    expect(TestModel::whereContains('colors', PureEnum::BLACK)->count())->toEqual(1);
    expect(TestModel::whereContains('colors', PureEnum::RED)->count())->toEqual(0);
    expect(TestModel::whereContains('colors', 'RED')->count())->toEqual(0);
    expect(TestModel::whereDoesntContain('colors', PureEnum::RED)->count())->toEqual(2);
    expect(TestModel::whereDoesntContain('colors', 'RED')->count())->toEqual(2);

    expect(TestModel::whereContains('json', PureEnum::YELLOW)->count())->toEqual(2);
    expect(TestModel::whereContains('json', PureEnum::BLACK)->count())->toEqual(1);
    expect(TestModel::whereContains('json', PureEnum::RED)->count())->toEqual(0);
    expect(TestModel::whereContains('json', 'RED')->count())->toEqual(0);
    expect(TestModel::whereDoesntContain('json', PureEnum::RED)->count())->toEqual(2);
    expect(TestModel::whereDoesntContain('json', 'RED')->count())->toEqual(2);

    expect(TestModel::whereContains('sizes', ['S'])->count())->toEqual(2);
    expect(TestModel::whereContains('sizes', 'S')->count())->toEqual(2);

    expect(TestModel::whereContains('visibilities', [IntBackedEnum::PUBLIC])->count())->toEqual(1);
    expect(TestModel::whereContains('visibilities', IntBackedEnum::PUBLIC)->count())->toEqual(1);
    expect(TestModel::whereContains('visibilities', 2)->count())->toEqual(1);

    expect(TestModel::whereContains('permissions', [LaravelEnum::PUBLIC])->count())->toEqual(1);
    expect(TestModel::whereContains('permissions', LaravelEnum::PUBLIC)->count())->toEqual(1);
    expect(TestModel::whereContains('permissions', 2)->count())->toEqual(1);

    expect(
        TestModel::whereContains('colors', PureEnum::BLACK)
            ->whereContains('colors', PureEnum::BLUE)->count()
    )->toEqual(1);

    expect(
        TestModel::whereContains('colors', PureEnum::BLACK)
            ->whereContains('colors', PureEnum::BLUE)->count()
    )->toEqual(1);

    expect(
        TestModel::whereContains('colors', [PureEnum::BLACK, PureEnum::BLUE])->count()
    )->toEqual(1);
    expect(
        TestModel::whereContains('colors', collect([PureEnum::BLACK, PureEnum::BLUE]))->count()
    )->toEqual(1);
    expect(
        TestModel::whereContains('colors', EnumCollection::make([PureEnum::BLACK, PureEnum::BLUE]))->count()
    )->toEqual(1);

    expect(
        TestModel::whereContains('colors', ['BLACK', 'BLUE'])->count()
    )->toEqual(1);

    expect(
        TestModel::whereContains('colors', PureEnum::RED)
            ->orWhereContains('sizes', StringBackedEnum::SMALL)->count()
    )->toEqual(2);

    expect(
        TestModel::whereContains('colors', 'RED')
            ->orWhereContains('sizes', StringBackedEnum::SMALL)->count()
    )->toEqual(2);
});

it('will return unique values when casting as unique and storing repeated values in the model', function () {
    $this->testModel->colors = [PureEnum::YELLOW, PureEnum::YELLOW, PureEnum::YELLOW];
    $this->testModel->save();

    $model = TestModel::find($this->testModel->id);

    expect($model->colors)->toBeInstanceOf(EnumCollection::class);
    expect($model->colors->toArray())->toEqual([PureEnum::YELLOW]);
});

it('stores unique values for backed enums', function () {
    $this->testModel->visibilities = [IntBackedEnum::PRIVATE, IntBackedEnum::PRIVATE, IntBackedEnum::PRIVATE];
    $this->testModel->save();

    $model = TestModel::find($this->testModel->id);

    expect($model->visibilities)->toBeInstanceOf(EnumCollection::class);
    expect($model->visibilities->toArray())->toEqual([IntBackedEnum::PRIVATE]);
});

it('stores unique values for backed enums, even with mixed enums and values', function () {
    $this->testModel->visibilities = ["1", 2, IntBackedEnum::PUBLIC];
    $this->testModel->save();

    $model = TestModel::find($this->testModel->id);

    expect($model->visibilities)->toBeInstanceOf(EnumCollection::class);
    expect($model->visibilities->toArray())->toEqual([IntBackedEnum::PRIVATE, IntBackedEnum::PUBLIC]);
});