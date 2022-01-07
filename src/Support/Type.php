<?php

namespace Soyhuce\NextIdeHelper\Support;

use function in_array;

class Type
{
    /** @var array<string> */
    private static array $builtIn = [
        'array',
        'bool',
        'callable',
        'int',
        'float',
        'object',
        'resource',
        'string',
        'mixed',
        'null',
    ];

    public static function qualify(string $type): string
    {
        $type = ltrim($type, '\\');
        if (self::isBuiltIn($type)) {
            return $type;
        }

        return '\\' . $type;
    }

    public static function isBuiltIn(string $type): bool
    {
        return in_array($type, self::$builtIn);
    }
}
