<?php

namespace Soyhuce\NextIdeHelper\Exceptions;

use Exception;

class DirectoryDoesNotExist extends Exception
{
    public function __construct(string $directory)
    {
        parent::__construct("Directory {$directory} does not exist");
    }
}
