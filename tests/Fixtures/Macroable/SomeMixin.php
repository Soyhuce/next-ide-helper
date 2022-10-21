<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Fixtures\Macroable;

use Illuminate\Support\Str;

class SomeMixin
{
    public function toLower()
    {
        /**
         * Convert some string to lowercase.
         *
         * @param string the input
         * @return string the input in lowercase
         */
        return static function (string $value): string {
            return Str::lower($value);
        };
    }

    public function havingVariadic()
    {
        return function (string &...$params): void {
            $params[] = 'foo';
            foreach ($params as $param) {
                echo $param;
            }
        };
    }

    public function havingSelfAsReturnType()
    {
        return function (): self {
            return $this;
        };
    }

    public function havingArrayAsDefaultValue()
    {
        return function (array $array = [1, 2, 3, ['some' => 'value']]): void {
            echo 'hello';
        };
    }
}
