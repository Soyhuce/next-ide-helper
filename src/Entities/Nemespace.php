<?php

namespace Soyhuce\NextIdeHelper\Entities;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Support\Output\WritesMultiline;

class Nemespace
{
    use WritesMultiline;

    private string $name;

    /** @var \Illuminate\Support\Collection<string, \Soyhuce\NextIdeHelper\Entities\Klass> */
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

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return Collection::make(["namespace {$this->name}", '{'])
            ->merge($this->classesLines())
            ->add('}')
            ->toArray();
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    protected function classesLines(): Collection
    {
        /** @var Collection<int, string> $lines */
        $lines = new Collection();

        $classes = $this->classes->sortBy(static fn (Klass $klass) => $klass->getName());
        foreach ($classes as $class) {
            $lines = $lines->merge(
                collect($class->toArray())
                    ->map(static fn (string $line) => $line ? str_repeat(' ', 4) . $line : $line)
            )->add('');
        }

        return $lines->splice(0, -1);
    }
}
