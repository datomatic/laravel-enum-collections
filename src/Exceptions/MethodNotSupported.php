<?php

declare(strict_types=1);

namespace Datomatic\EnumCollections\Exceptions;

use Exception;

class MethodNotSupported extends Exception
{
    public function __construct(string $method)
    {
        parent::__construct('Unsupported method: '.$method);
    }
}
