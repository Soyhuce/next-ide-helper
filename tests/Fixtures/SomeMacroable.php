<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Illuminate\Support\Traits\Macroable;

class SomeMacroable
{
    use Macroable;

    public function __construct(int $foo)
    {
        $this->plop = $foo;
    }
}
