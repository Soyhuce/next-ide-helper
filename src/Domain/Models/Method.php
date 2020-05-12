<?php

namespace Soyhuce\NextIdeHelper\Domain\Models;

class Method
{
    public string $name;

    /** @var array<string> */
    public array $parameters;

    public ?string $returnType;

    public function __construct(string $name, array $parameters = [], ?string $returnType = null)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->returnType = $returnType;
    }
}
