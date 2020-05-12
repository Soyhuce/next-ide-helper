<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Illuminate\Support\Str;

class SomeMixin
{
    public function toLower()
    {
        return function (string $value): string {
            return Str::lower($value);
        };
    }
}
