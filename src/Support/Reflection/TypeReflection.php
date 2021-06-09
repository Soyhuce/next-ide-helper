<?php

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
                ->map(fn (ReflectionType $type) => self::asString($type))
                ->implode('|');
        }

        if ($type instanceof ReflectionNamedType) {
            $name = $type->getName();
        } else {
            $name = (string) $type;
        }

        if ($type->isBuiltin()) {
            return $name;
        }

        if ($name === 'self') {
            return $name;
        }

        return '\\' . $name;
    }
}
