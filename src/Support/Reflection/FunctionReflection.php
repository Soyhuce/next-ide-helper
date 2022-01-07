<?php

namespace Soyhuce\NextIdeHelper\Support\Reflection;

use Illuminate\Support\Str;
use ReflectionFunctionAbstract;
use function array_slice;

class FunctionReflection
{
    /**
     * @return array<string>|null
     */
    public static function docblock(ReflectionFunctionAbstract $function): ?array
    {
        $docBlock = $function->getDocComment();
        if ($docBlock === false) {
            return null;
        }

        $lines = Str::of($docBlock)->explode(PHP_EOL);

        $spaces = Str::length($lines->last()) - Str::length(ltrim($lines->last(), ' ')) - 1;

        return $lines->splice(1, -1)
            ->map(static fn (string $line) => Str::substr($line, $spaces))
            ->toArray();
    }

    public static function isStatic(ReflectionFunctionAbstract $function): bool
    {
        return $function->getClosureThis() === null;
    }

    public static function parameters(ReflectionFunctionAbstract $function): string
    {
        return implode(', ', static::parameterList($function));
    }

    /**
     * @return array<string>
     */
    public static function parameterList(ReflectionFunctionAbstract $function): array
    {
        $parameters = [];
        foreach ($function->getParameters() as $parameter) {
            $parameters[] = ParameterReflection::asString($parameter);
        }

        return $parameters;
    }

    public static function returnType(ReflectionFunctionAbstract $function): ?string
    {
        if (!$function->hasReturnType()) {
            return null;
        }

        $type = $function->getReturnType();

        $returnType = TypeReflection::asString($type);

        if ($type->allowsNull()) {
            $returnType = '?' . $returnType;
        }

        return $returnType;
    }

    /**
     * @return array<string>
     */
    public static function bodyLines(ReflectionFunctionAbstract $function): array
    {
        $filename = $function->getFileName();
        $start_line = $function->getStartLine();
        $end_line = $function->getEndLine() - 1;
        $length = $end_line - $start_line;

        if ($length < 1) {
            return [];
        }

        $lines = collect(array_slice(file($filename), $start_line, $length));
        $spaces = max(Str::length($lines->last()) - Str::length(ltrim($lines->last(), ' ')) - 4, 0);

        return $lines->map(
            static fn (string $line) => rtrim(Str::of($line)->substr($spaces), PHP_EOL)
        )->toArray();
    }
}
