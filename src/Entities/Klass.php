<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Entities;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Support\Type;

class Klass
{
    private string $name;

    private ?string $extends = null;

    /** @var \Illuminate\Support\Collection<int, string> */
    private Collection $docTags;

    /** @var \Illuminate\Support\Collection<int, \Soyhuce\NextIdeHelper\Entities\Method> */
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

    public function addMethod(Method $method): self
    {
        if ($this->methods->firstWhere('name', $method->name) === null) {
            $this->methods->add($method);
        }

        return $this;
    }

    public function toString(): string
    {
        $result = PHP_EOL;

        if ($this->docTags->isNotEmpty()) {
            $result .= $this->docTags
                ->prepend('/**')
                ->push(' */')
                ->map(static fn (string $tag): string => "    {$tag}")
                ->implode(PHP_EOL) . PHP_EOL;
        }

        $result .= "    class {$this->name}";

        if ($this->extends !== null) {
            $result .= ' extends ' . Type::qualify($this->extends);
        }

        $result .= ' {';

        if ($this->methods->isNotEmpty()) {
            $result .= PHP_EOL .
                $this->methods->sort()
                    ->map(static fn (Method $method) => '        ' . $method->name)
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
        /** @var Collection<int, string> $collection */
        $collection = new Collection();

        return $collection
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
            return new Collection();
        }

        return (new Collection($this->docTags))
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
        /** @var Collection<int, string> $lines */
        $lines = new Collection();

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
