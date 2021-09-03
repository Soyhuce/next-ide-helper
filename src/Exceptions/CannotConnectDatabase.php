<?php

namespace Soyhuce\NextIdeHelper\Exceptions;

use Exception;

class CannotConnectDatabase extends Exception
{
    public function __construct(string $table, ?string $connection)
    {
        if ($connection === null) {
            $connection = 'default';
        }

        parent::__construct("Cannot connect on table {$table} of {$connection} connection.");
    }
}
