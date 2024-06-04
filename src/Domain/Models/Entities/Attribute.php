<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Support\Str;

class Attribute
{
    public string $name;

    public string $type;

    public bool $nullable;

    public bool $readOnly = false;

    public bool $writeOnly = false;

    public bool $inDatabase = false;

    public bool $nullableInDatabase = false;

    public ?string $comment = null;

    public function __construct(string $name, string $type, ?string $comment = null)
    {
        $this->name = $name;
        $this->setType($type);
        $this->comment = $comment;
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
