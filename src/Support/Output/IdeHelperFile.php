<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support\Output;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Entities\Klass;
use Soyhuce\NextIdeHelper\Entities\Nemespace;

class IdeHelperFile
{
    use WritesMultiline;

    private string $filePath;

    /** @var \Illuminate\Support\Collection<string, \Soyhuce\NextIdeHelper\Entities\Nemespace> */
    private Collection $namespaces;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->namespaces = new Collection();
    }

    public function getOrAddClass(string $fqcn): Klass
    {
        $namespace = (string) Str::of($fqcn)
            ->start('\\')
            ->beforeLast('\\')
            ->trim('\\');
        $class = Str::afterLast($fqcn, '\\');

        if (!str_contains($class, '__')) {
            $class = 'NextIdeHelper' . $class;
        }

        return $this->getOrAddNamespace($namespace)->getOrAddClass($class);
    }

    private function getOrAddNamespace(string $name): Nemespace
    {
        $namespace = $this->namespaces->get($name);

        if ($namespace === null) {
            $namespace = new Nemespace($name);
            $this->namespaces->put($name, $namespace);
        }

        return $namespace;
    }

    public function render(): void
    {
        $lines = Collection::make(['<?php', '']);

        $namespaces = $this->namespaces->sortBy(static fn (Nemespace $namespace) => $namespace->getName());
        foreach ($namespaces as $namespace) {
            $lines = $lines->merge($namespace->toArray())->add('');
        }

        $content = $lines->map(fn (string $line) => rtrim($line, ' '))->implode(PHP_EOL);
        File::put($this->filePath, $content);
    }
}
