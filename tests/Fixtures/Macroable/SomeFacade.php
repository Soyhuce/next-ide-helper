<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures\Macroable;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Traits\Macroable;

class SomeFacade extends Facade
{
    use Macroable;

    protected static function getFacadeAccessor()
    {
        return SomeMacroable::class;
    }
}
