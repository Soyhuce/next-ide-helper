<?php

namespace Soyhuce\NextIdeHelper\Domain\Output\HelperFile;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Output\WritesMultiline;

class IdeHelperFile
{
    use WritesMultiline;

    private string $filePath;

    private Collection $namespaces;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->namespaces = collect();
    }

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

    public function getOrAddClass(string $fqcn): ClassHelper
    {
        $namespace = (string) Str::of($fqcn)->beforeLast('\\')->trim('\\');
        $class = Str::afterLast($fqcn, '\\');

        return $this->getOrAddNamespace($namespace)->getOrAddClass($class);
    }

    private function getOrAddNamespace(string $name): NamespaceHelper
    {
        $namespace = $this->namespaces->get($name);

        if ($namespace === null) {
            $namespace = new NamespaceHelper($name);
            $this->namespaces->put($name, $namespace);
        }

        return $namespace;
    }

    public function render(): void
    {
        $content = Collection::make([
            '<?php',
        ])
            ->merge(
                $this->namespaces
                    ->sortBy(fn (NamespaceHelper $namespaceHelper) => $namespaceHelper->getName())
                    ->map(fn (NamespaceHelper $namespaceHelper) => $namespaceHelper->toString())
            )
            ->map(fn (string $line): string => $this->line($line))
            ->implode(PHP_EOL);

        File::put($this->filePath, $content);
    }
}
