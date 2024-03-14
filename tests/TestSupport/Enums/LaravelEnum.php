<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Tests\TestSupport\Enums;

use Datomatic\LaravelEnumHelper\LaravelEnumHelper;

enum LaravelEnum: int
{
    use LaravelEnumHelper;
    case PRIVATE = 1;

    case PUBLIC = 2;

    case PROTECTED = 3;
}
