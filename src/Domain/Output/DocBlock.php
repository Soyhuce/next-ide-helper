<?php

namespace Soyhuce\NextIdeHelper\Domain\Output;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;

class DocBlock
{
    use WritesMultiline;

    protected function replacePreviousBlock(string $docBlock, string $fqcn, string $file)
    {
        $content = File::get($file);
        $previousDocBlock = $this->previousDocBlock($fqcn);

        $class = Str::afterLast($fqcn, '\\');

        $updatedContent = str_replace(
            "{$previousDocBlock}class {$class}",
            "{$docBlock}class {$class}",
            $content
        );

        File::put($file, $updatedContent);
    }

    private function previousDocBlock(string $fqcn): string
    {
        $docBlock = (new ReflectionClass($fqcn))->getDocComment();

        if ($docBlock === false) {
            return '';
        }

        return $docBlock . PHP_EOL;
    }
}
