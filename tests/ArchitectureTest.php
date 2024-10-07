<?php

declare(strict_types=1);

arch()->preset()->php();

arch()
    ->expect('App')
    ->toUseStrictTypes()
    ->not->toUse(['die', 'dd', 'dump']);
