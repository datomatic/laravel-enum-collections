<?php

namespace Datomatic\EnumCollection\Commands;

use Illuminate\Console\Command;

class EnumCollectionCommand extends Command
{
    public $signature = 'laravel-enum-collections';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
