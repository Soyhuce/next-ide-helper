<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Fixtures\Macroable;

use Illuminate\Support\Facades\Facade;

class SomeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SomeMacroable::class;
    }
}
