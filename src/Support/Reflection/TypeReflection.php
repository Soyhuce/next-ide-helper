<?php

namespace Soyhuce\NextIdeHelper\Support\Reflection;

use ReflectionNamedType;
use ReflectionType;

class TypeReflection
{
    public static function asString(ReflectionType $type): string
    {
        if ($type instanceof ReflectionNamedType) {
            $name = $type->getName();
        } else {
            $name = (string) $type;
        }
        if (!$type->isBuiltin()) {
            $name = '\\' . $name;
        }

        return $name;
    }
}
