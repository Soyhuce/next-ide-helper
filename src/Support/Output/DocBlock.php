<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support\Output;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;

class DocBlock
{
    use WritesMultiline;

    /**
     * @param class-string $fqcn
     */
    protected function replacePreviousBlock(string $docBlock, string $fqcn, string $file): void
    {
        $content = File::get($file);
        $previousDocBlock = $this->previousDocBlock($fqcn);

        if ($previousDocBlock === null) {
            $class = Str::afterLast($fqcn, '\\');
            $classDeclaration = $this->classDeclaration($content, $class);

            $updatedContent = Str::replaceFirst(
                $classDeclaration,
                "{$docBlock}{$classDeclaration}",
                $content
            );
        } else {
            $docBlock = $this->injectUserDefinedTags($docBlock, $previousDocBlock);

            $updatedContent = Str::replaceFirst($previousDocBlock, $docBlock, $content);
        }

        File::put($file, $updatedContent);
    }

    /**
     * @param class-string $fqcn
     */
    private function previousDocBlock(string $fqcn): ?string
    {
        $docBlock = (new ReflectionClass($fqcn))->getDocComment();

        if ($docBlock === false) {
            return null;
        }

        return $docBlock . PHP_EOL;
    }

    private function classDeclaration(string $content, string $class): string
    {
        return Collection::make(explode(PHP_EOL, $content))->first(
            fn (string $line): bool => Str::contains($line, "class {$class}"),
            "class {$class}"
        );
    }

    private function injectUserDefinedTags(string $docBlock, string $previousDocBlock): string
    {
        if (!str_contains($previousDocBlock, '@generated')) {
            return $docBlock;
        }

        $header = Str::beforeLast($previousDocBlock, '@generated' . PHP_EOL);

        return str_replace('/**' . PHP_EOL, $header . '@generated' . PHP_EOL, $docBlock);
    }
}
