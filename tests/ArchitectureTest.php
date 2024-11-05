<?php

declare(strict_types=1);

if (function_exists('arch')) {
    arch()->preset()->php();

    arch()
        ->expect('Datomatic\EnumCollections')
        ->toUseStrictTypes()
        ->not->toUse(['die', 'dd', 'dump']);
}
