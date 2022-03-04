<?php declare(strict_types=1);

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

    /**
     * @return array<string>
     */
    public static function bodyLines(ReflectionFunctionAbstract $function): array
    {
        $filename = $function->getFileName();
        if ($filename === false) {
            return [];
        }

        $file = file($filename);
        if ($file === false) {
            return [];
        }

        $startLine = $function->getStartLine();
        $endLine = $function->getEndLine();
        $length = $endLine - $startLine - 1;
        if ($startLine === false || $endLine === false || $length < 1) {
            return [];
        }

        $lines = collect(array_slice($file, $startLine, $length));
        $lastLine = $lines->last();
        if ($lastLine === null) {
            return [];
        }

        $spaces = max(Str::length($lastLine) - Str::length(ltrim($lastLine, ' ')) - 4, 0);

        return $lines->map(
            static fn (string $line) => Str::of($line)->substr($spaces)->rtrim(PHP_EOL)->toString()
        )->toArray();
    }
}
