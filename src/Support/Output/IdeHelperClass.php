<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support\Output;

use Illuminate\Support\Str;

class IdeHelperClass
{
    /**
     * @param class-string<\Illuminate\Database\Eloquent\Model> $modelFqcn
     * @return class-string<\Illuminate\Database\Eloquent\Builder>
     */
    public static function eloquentBuilder(string $modelFqcn): string
    {
        $classBasename = class_basename($modelFqcn);

        return (string) Str::of($modelFqcn)->trim('\\')
            ->beforeLast($classBasename)
            ->prepend('\\IdeHelper\\')
            ->append("__{$classBasename}Query");
    }

    public static function relation(string $modelFqcn, string $relationName): string
    {
        return (string) Str::of($modelFqcn)->trim('\\')
            ->prepend('\\IdeHelper\\')
            ->append(Str::of($relationName)->studly()->prepend('\\__'));
    }
}
