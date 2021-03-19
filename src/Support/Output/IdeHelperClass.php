<?php

namespace Soyhuce\NextIdeHelper\Support\Output;

use Illuminate\Support\Str;

class IdeHelperClass
{
    public static function eloquentBuilder(string $modelFqcn): string
    {
        return (string) Str::of($modelFqcn)->trim('\\')
            ->prepend('\\IdeHelper\\')->append('Query');
    }

    public static function relation(string $modelFqcn, string $relationName): string
    {
        return (string) Str::of($modelFqcn)->trim('\\')
            ->prepend('\\IdeHelper\\')
            ->append(Str::of($relationName)->studly()->prepend('\\'));
    }
}
