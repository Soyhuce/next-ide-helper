<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support\Reflection;

use Illuminate\Support\Str;
use ReflectionFunctionAbstract;

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
        $type = $function->getReturnType();

        if ($type === null) {
            return null;
        }

        $returnType = TypeReflection::asString($type);

        if ($type->allowsNull()) {
            $returnType = '?' . $returnType;
        }

        return $returnType;
    }

    public static function source(ReflectionFunctionAbstract $function): ?string
    {
        $filename = $function->getFileName();
        if ($filename === false) {
            return '';
        }

        return $filename;
    }

    public static function line(ReflectionFunctionAbstract $function): ?int
    {
        $line = $function->getStartLine();
        if ($line === false) {
            return null;
        }

        return $line;
    }
}
