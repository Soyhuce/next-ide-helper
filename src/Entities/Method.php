<?php

namespace Soyhuce\NextIdeHelper\Entities;

use Illuminate\Support\Collection;
use ReflectionFunction;
use Soyhuce\NextIdeHelper\Support\Reflection\FunctionReflection;

class Method
{
    public string $name;

    public ?array $docblock = null;

    public ?string $parameters = null;

    public ?string $returnType = null;

    public ?array $body = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function new(string $name): self
    {
        return new self($name);
    }

    public static function fromFunction(string $name, ReflectionFunction $function): self
    {
        return self::new($name)
            ->docblock(FunctionReflection::docblock($function))
            ->parameters(FunctionReflection::parameters($function))
            ->returnType(FunctionReflection::returnType($function))
            ->body(FunctionReflection::bodyLines($function));
    }

    public function docblock(?array $docblock): self
    {
        $this->docblock = $docblock;

        return $this;
    }

    public function parameters(?string $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    public function returnType(?string $returnType): self
    {
        $this->returnType = $returnType;

        return $this;
    }

    public function body(?array $body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return collect()
            ->merge($this->docblockLines())
            ->add($this->definition())
            ->add('{')
            ->merge($this->bodyLines())
            ->add('}')
            ->toArray();
    }

    private function docblockLines(): Collection
    {
        if ($this->docblock === null) {
            return collect();
        }

        return collect($this->docblock)
            ->prepend('/**')
            ->add('*/');
    }

    private function definition(): string
    {
        $definition = "public function {$this->name}({$this->parameters})";

        if ($this->returnType === null) {
            return $definition;
        }

        return $definition . ": {$this->returnType}";
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function bodyLines(): Collection
    {
        if ($this->body === null) {
            return collect();
        }

        return collect($this->body);
    }
}
