<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Exceptions;

use Exception;

class NotImplementedYet extends Exception
{
    public function __construct(?string $reason)
    {
        parent::__construct("Not implemented yet but you can contribute ! {$reason}");
    }
}
