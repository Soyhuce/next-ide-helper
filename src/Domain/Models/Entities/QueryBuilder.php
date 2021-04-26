<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Support\Type;

class QueryBuilder
{
    public string $fqcn;

    public string $filePath;

    public Collection $extras;

    public function __construct(string $fqcn, string $filePath)
    {
        $this->fqcn = Type::qualify($fqcn);
        $this->filePath = $filePath;
        $this->extras = collect();
    }

    public function isBuiltIn()
    {
        return $this->fqcn === '\\' . EloquentBuilder::class;
    }

    public function addExtra(string $extra): self
    {
        $this->extras->add($extra);

        return $this;
    }
}
