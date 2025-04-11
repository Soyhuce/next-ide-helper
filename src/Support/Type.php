<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        // Not built-in but acts like one for PHPStan
        'positive-int',
        'negative-int',
        'non-positive-int',
        'non-negative-int',
        'non-zero-int',
        'non-empty-array',
        'list',
        'non-empty-list',
        'key-of',
        'value-of',
    ];

    /**
     * @template T of string
     * @param T $type
     * @return T
     */
    public static function qualify(string $type): string
    {
        $type = Str::ltrim($type, '\\');
        if (self::isBuiltIn($type)) {
            return $type;
        }

        return '\\' . $type;
    }

    public static function isBuiltIn(string $type): bool
    {
        return Collection::make(self::$builtIn)->contains(fn (string $pattern) => preg_match("#^{$pattern}#", $type));
    }
}
