<?php

namespace Soyhuce\NextIdeHelper\Support;

class Type
{
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
