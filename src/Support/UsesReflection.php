<?php

namespace Soyhuce\NextIdeHelper\Support;

use Illuminate\Support\Str;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;

trait UsesReflection
{
    protected function typeName(ReflectionType $type): string
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

    protected function parameterString(ReflectionParameter $parameter): string
    {
        $export = '';
        if ($parameter->hasType()) {
            if ($parameter->allowsNull()) {
                $export .= '?';
            }
            $export .= $this->typeName($parameter->getType()) . ' ';
        } elseif ($parameter->isVariadic()) {
            $export .= '...';
        }

        if ($parameter->isPassedByReference()) {
            $export .= '&';
        }

        $export .= '$' . $parameter->getName();

        if ($parameter->isOptional()) {
            $export .= ' = ';
            if ($parameter->isDefaultValueAvailable()) {
                if ($parameter->isDefaultValueConstant()) {
                    $export .= $parameter->getDefaultValueConstantName();
                } else {
                    $export .= Str::lower(var_export($parameter->getDefaultValue(), true));
                }
            } else {
                $export .= 'null';
            }
        }

        return $export;
    }

    private function extractOrGuessReturnType(ReflectionMethod $method): ?string
    {
        if ($method->hasReturnType()) {
            return $this->typeName($method->getReturnType());
        }

        $doc = $method->getDocComment();
        if ($doc === false) {
            return null;
        }

        $match = Str::of($doc)->match('/@return ([^|\n]*)/');
        if ($match === false) {
            return null;
        }

        return $match;
    }
}
