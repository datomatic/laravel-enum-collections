<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Tests;

use Datomatic\EnumCollections\EnumCollectionServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [
            EnumCollectionServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app) {}

    protected function setUpDatabase()
    {
        if (! Schema::hasTable('test_models')) {
            Schema::create('test_models', function (Blueprint $table) {
                $table->increments('id');
                $table->json('visibilities')->nullable();
                $table->json('permissions')->nullable();
                $table->json('colors')->nullable();
                $table->json('sizes')->nullable();
                $table->json('json')->nullable();
            });
        }
    }
}
