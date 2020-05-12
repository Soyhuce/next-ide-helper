<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

class Attribute
{
    public string $name;

    public string $type;

    public bool $nullable;

    public bool $readOnly = false;

    public ?string $comment = null;

    public function __construct(string $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
        $this->nullable = false;
    }

    public function toUnionType(): string
    {
        if (!$this->nullable) {
            return $this->type;
        }

        return "{$this->type}|null";
    }
}
