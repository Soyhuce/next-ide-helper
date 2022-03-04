<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Exceptions;

use Exception;

class CannotFindClassFile extends Exception
{
    public function __construct(string $class)
    {
        parent::__construct("Cannot find file for class {$class}");
    }
}
