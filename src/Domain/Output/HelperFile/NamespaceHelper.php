<?php

namespace Soyhuce\NextIdeHelper\Domain\Output\HelperFile;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Output\WritesMultiline;

class NamespaceHelper
{
    use WritesMultiline;

    private string $name;

    private Collection $classes;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->classes = new Collection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getOrAddClass(string $name): ClassHelper
    {
        $class = $this->classes->get($name);

        if ($class === null) {
            $class = new ClassHelper($name);
            $this->classes->put($name, $class);
        }

        return $class;
    }

    public function toString(): string
    {
        return Collection::make(["namespace {$this->name} {"])
            ->merge(
                $this->classes->sortBy(static fn (ClassHelper $classHelper) => $classHelper->getName())
                    ->map(static fn (ClassHelper $classHelper) => $classHelper->toString())
            )
            ->add('}')
            ->implode(PHP_EOL);
    }
}
