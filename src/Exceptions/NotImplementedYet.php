<?php

namespace Soyhuce\NextIdeHelper\Exceptions;

class NotImplementedYet extends \Exception
{
    public function __construct(?string $reason)
    {
        parent::__construct("Not implemented yet but you can contribute ! ${reason}");
    }
}
