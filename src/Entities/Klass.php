<?php

namespace Soyhuce\NextIdeHelper\Entities;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Klass
{
    private string $name;

    private ?string $extends = null;

    private Collection $docTags;

    private Collection $methods;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->docTags = new Collection();
        $this->methods = new Collection();
    }

    public function extends(string $class): self
    {
        $this->extends = $class;

        return $this;
    }

    public function addDocTag(string $docTag): self
    {
        $this->docTags->add($docTag);

        return $this;
    }

    public function addDocTags(Collection $docTags): self
    {
        $this->docTags = $this->docTags->merge($docTags);

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addMethod(Method $method)
    {
        $this->methods->add($method);
    }

    public function toString(): string
    {
        $result = PHP_EOL;

        if ($this->docTags->isNotEmpty()) {
            $result .= $this->docTags
                ->prepend('/**')
                ->push(' */')
                ->map(static fn (string $tag): string => "    ${tag}")
                ->implode(PHP_EOL) . PHP_EOL;
        }

        $result .= "    class {$this->name}";

        if ($this->extends !== null) {
            $result .= ' extends ' . Str::start($this->extends, '\\');
        }

        $result .= ' {';

        if ($this->methods->isNotEmpty()) {
            $result .= PHP_EOL .
                $this->methods->sort()
                    ->map(static fn (string $function) => '        ' . $function)
                    ->implode(PHP_EOL . PHP_EOL)
                . PHP_EOL;
            $result .= '    }';
        } else {
            $result .= '}';
        }

        return $result;
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return collect()
            ->merge($this->docblockLines())
            ->add($this->classDefinition())
            ->add('{')
            ->merge($this->methodsLines())
            ->add('}')
            ->toArray();
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function docblockLines(): Collection
    {
        if ($this->docTags->isEmpty()) {
            return collect();
        }

        return collect($this->docTags)
            ->prepend('/**')
            ->push(' */');
    }

    private function classDefinition(): string
    {
        $definition = "class {$this->name}";

        if ($this->extends === null) {
            return $definition;
        }

        return $definition . ' extends ' . Str::start($this->extends, '\\');
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function methodsLines(): Collection
    {
        $lines = collect();

        $methods = $this->methods->sortBy(static fn (Method $method) => $method->name);
        foreach ($methods as $method) {
            $lines = $lines->merge(
                collect($method->toArray())
                    ->map(static fn (string $line) => $line ? str_repeat(' ', 4) . $line : $line)
            )->add('');
        }

        return $lines->splice(0, -1);
    }
}
