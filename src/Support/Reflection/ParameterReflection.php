<?php

namespace Soyhuce\NextIdeHelper\Support\Reflection;

use Illuminate\Support\Str;
use ReflectionParameter;

class ParameterReflection
{
    public static function asString(ReflectionParameter $parameter): string
    {
        $export = '';
        if ($parameter->hasType()) {
            if ($parameter->allowsNull()) {
                $export .= '?';
            }
            $export .= TypeReflection::asString($parameter->getType()) . ' ';
        }

        if ($parameter->isPassedByReference()) {
            $export .= '&';
        }

        if ($parameter->isVariadic()) {
            $export .= '...';
        }

        $export .= '$' . $parameter->getName();

        if ($parameter->isOptional() && !$parameter->isVariadic()) {
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
}
