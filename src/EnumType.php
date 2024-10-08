<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections;

enum EnumType
{
    case UNIT_ENUM;
    case STRING_BACKED_ENUM;
    case INT_BACKED_ENUM;
}
