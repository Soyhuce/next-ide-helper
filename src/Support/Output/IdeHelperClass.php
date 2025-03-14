<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support\Output;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IdeHelperClass
{
    /**
     * @param class-string<Model> $modelFqcn
     * @return class-string<Builder<Model>>
     */
    public static function eloquentBuilder(string $modelFqcn): string
    {
        $classBasename = class_basename($modelFqcn);

        return Str::of($modelFqcn)
            ->trim('\\')
            ->beforeLast($classBasename)
            ->prepend('\\IdeHelper\\')
            ->append("__{$classBasename}Query")
            ->toString();
    }

    public static function relation(string $modelFqcn, string $relationName): string
    {
        return Str::of($modelFqcn)
            ->trim('\\')
            ->prepend('\\IdeHelper\\')
            ->append(Str::of($relationName)->studly()->prepend('\\__')->toString())
            ->toString();
    }
}
