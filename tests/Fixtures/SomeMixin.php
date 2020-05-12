<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Illuminate\Support\Str;

class SomeMixin
{
    public function toLower()
    {
        /**
         * Convert some string to lowercase
         *
         * @param string the input
         * @return string the input in lowercase
         */
        return function (string $value): string {
            return Str::lower($value);
        };
    }
}
