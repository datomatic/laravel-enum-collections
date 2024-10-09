<?php

declare(strict_types=1);

arch()->preset()->php();

arch()
    ->expect('Datomatic\EnumCollections')
    ->toUseStrictTypes()
    ->not->toUse(['die', 'dd', 'dump']);