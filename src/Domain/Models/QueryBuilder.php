<?php

namespace Soyhuce\NextIdeHelper\Domain\Models;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Str;

class QueryBuilder
{
    public string $fqcn;

    public string $filePath;

    public function __construct(string $fqcn, string $filePath)
    {
        $this->fqcn = Str::start($fqcn, '\\');
        $this->filePath = $filePath;
    }

    public function isBuiltIn()
    {
        return $this->fqcn === '\\' . EloquentBuilder::class;
    }
}
