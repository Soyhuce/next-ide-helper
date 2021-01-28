<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Support\Str;

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
        $this->setType($type);
        $this->nullable = false;
    }

    public function setType(string $type): void
    {
        if (Str::startsWith($type, '?')) {
            $type = Str::replaceFirst('?', '', $type);
            $this->nullable = true;
        }

        $this->type = $type;
    }

    public function toUnionType(): string
    {
        if (!$this->nullable) {
            return $this->type;
        }

        return "{$this->type}|null";
    }
}
