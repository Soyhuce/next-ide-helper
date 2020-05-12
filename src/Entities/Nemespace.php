<?php

namespace Soyhuce\NextIdeHelper\Entities;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Entities\Klass;
use Soyhuce\NextIdeHelper\Support\Output\WritesMultiline;

class Nemespace
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

    public function getOrAddClass(string $name): Klass
    {
        $class = $this->classes->get($name);

        if ($class === null) {
            $class = new Klass($name);
            $this->classes->put($name, $class);
        }

        return $class;
    }

    public function toString(): string
    {
        return Collection::make(["namespace {$this->name} {"])
            ->merge(
                $this->classes->sortBy(static fn (Klass $classHelper) => $classHelper->getName())
                    ->map(static fn (Klass $classHelper) => $classHelper->toString())
            )
            ->add('}')
            ->implode(PHP_EOL);
    }
}
