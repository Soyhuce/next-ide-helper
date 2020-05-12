<?php

namespace Soyhuce\NextIdeHelper\Support;

use Illuminate\Support\Str;
use ReflectionFunction;

class FunctionExporter
{
    public static function docblock(ReflectionFunction $function): ?array
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

    public static function parameters(ReflectionFunction $function): string
    {
        $parameters = [];
        foreach ($function->getParameters() as $parameter) {
            $type = $parameter->hasType() ? $parameter->getType()->getName() : '';
            $variadic = $parameter->isVariadic() ? '...' : '';
            $name = '$' . $parameter->getName();
            $optional = !$parameter->isVariadic() && $parameter->isOptional() ?
                '= ' . str_replace(PHP_EOL, ' ', var_export($parameter->getDefaultValue(), true)) :
                '';

            $parameters[] = trim("${type} ${variadic} ${name} ${optional}");
        }

        return implode(', ', $parameters);
    }

    public static function returnType(ReflectionFunction $function): ?string
    {
        if (!$function->hasReturnType()) {
            return null;
        }

        $type = $function->getReturnType();

        $returnType = $type->getName();

        if (!$type->isBuiltin()) {
            $returnType = '\\' . $returnType;
        }

        if ($type->allowsNull()) {
            $returnType = '?' . $returnType;
        }

        return $returnType;
    }

    public static function body(ReflectionFunction $function): array
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
            static fn (string $line) => (string) Str::of($line)->substr($spaces)->rtrim(PHP_EOL)
        )->toArray();
    }
}
