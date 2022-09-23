<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Tests\TestSupport\Enums;

enum StringBackedEnum: string
{
    case SMALL = 'S';

    case MEDIUM = 'M';

    case LARGE = 'L';

    case EXTRA_LARGE = 'XL';

}
