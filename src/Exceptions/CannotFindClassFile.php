<?php

namespace Soyhuce\NextIdeHelper\Exceptions;

class CannotFindClassFile extends \Exception
{
    public function __construct(string $class)
    {
        parent::__construct("Cannot find file for class {$class}");
    }
}
