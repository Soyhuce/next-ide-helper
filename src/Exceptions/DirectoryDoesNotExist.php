<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Exceptions;

use Exception;

class DirectoryDoesNotExist extends Exception
{
    public function __construct(string $directory)
    {
        parent::__construct("Directory {$directory} does not exist");
    }
}
