<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support\Reflection;

use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

class TypeReflection
{
    public static function asString(ReflectionType $type): string
    {
        if ($type instanceof ReflectionUnionType) {
            return collect($type->getTypes())
                ->map(fn (ReflectionNamedType $type) => self::asString($type))
                ->implode('|');
        }

        if (!$type instanceof ReflectionNamedType) {
            return (string) $type;
        }

        $name = $type->getName();

        if ($type->isBuiltin()) {
            return $name;
        }

        if ($name === 'self') {
            return $name;
        }

        return '\\' . $name;
    }
}
