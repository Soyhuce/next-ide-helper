<?php

namespace Soyhuce\NextIdeHelper\Support\Output;

class FunctionExporter
{
    public static function parameters(\ReflectionFunction $function): string
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

    public static function body(\ReflectionFunction $function)
    {
        $filename = $function->getFileName();
        $start_line = $function->getStartLine();
        $end_line = $function->getEndLine() - 1;
        $length = $end_line - $start_line;

        $source = file($filename);

        return implode('', array_slice($source, $start_line, $length));
    }

    public static function returnType(\ReflectionFunction $function)
    {
        if (!$function->hasReturnType()) {
            return;
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
}
