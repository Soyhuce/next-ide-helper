<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Fixtures\Macroable;

use Illuminate\Support\Traits\Macroable;

class SomeMacroable
{
    use Macroable;

    public function __construct(int $foo)
    {
        $this->plop = $foo;
    }
}
