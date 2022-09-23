<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Tests\TestSupport\Enums;

enum IntBackedEnum: int
{
    case PRIVATE = 1;

    case PUBLIC = 2;

    case PROTECTED = 3;

}
