<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Tests\TestSupport\Enums;

use Datomatic\EnumHelper\EnumHelper;
use Datomatic\EnumHelper\Traits\EnumDescription;
use Datomatic\EnumHelper\Traits\EnumUniqueId;

enum PureEnum
{

    case YELLOW;

    case WHITE;

    case BLACK;

    case RED;

    case GREEN;

    case BLUE;

}
