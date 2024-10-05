<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Tests\TestSupport\Enums;

enum PureEnum
{
    case YELLOW;

    case WHITE;

    case BLACK;

    case RED;

    case GREEN;

    case BLUE;

    public function next()
    {
        return match ($this) {
            PureEnum::YELLOW => PureEnum::WHITE,
            PureEnum::WHITE => PureEnum::BLACK,
            PureEnum::BLACK => PureEnum::RED,
            PureEnum::RED => PureEnum::GREEN,
            PureEnum::GREEN => PureEnum::BLUE,
            PureEnum::BLUE => PureEnum::YELLOW,
        };
    }
}
