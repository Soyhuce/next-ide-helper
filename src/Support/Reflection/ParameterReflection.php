<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support\Reflection;

use Illuminate\Support\Arr;
use ReflectionNamedType;
use ReflectionParameter;
use function is_array;

class ParameterReflection
{
    public static function asString(ReflectionParameter $parameter): string
    {
        $export = '';
        $type = $parameter->getType();
        if ($type !== null) {
            if (
                $parameter->allowsNull()
                && $type instanceof ReflectionNamedType
                && $type->getName() !== 'mixed'
            ) {
                $export .= '?';
            }
            $export .= TypeReflection::asString($type) . ' ';
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
                    $export .= self::exportValue($parameter->getDefaultValue());
                }
            } else {
                $export .= 'null';
            }
        }

        return $export;
    }

    private static function exportValue(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }
        if (!is_array($value)) {
            return var_export($value, true);
        }
        if (!Arr::isAssoc($value)) {
            return '[' .
                collect($value)
                    ->map(function ($item): string {
                        return self::exportValue($item);
                    })
                    ->implode(', ')
                . ']';
        }

        return '[' .
            collect($value)
                ->map(function ($item, $key): string {
                    return self::exportValue($key) . ' => ' . self::exportValue($item);
                })
                ->implode(', ')
            . ']';
    }
}
