<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Soyhuce\NextIdeHelper\Support\Type;

class QueryBuilder
{
    public string $fqcn;

    public string $filePath;

    public function __construct(string $fqcn, string $filePath)
    {
        $this->fqcn = Type::qualify($fqcn);
        $this->filePath = $filePath;
    }

    public function isBuiltIn()
    {
        return $this->fqcn === '\\' . EloquentBuilder::class;
    }
}
