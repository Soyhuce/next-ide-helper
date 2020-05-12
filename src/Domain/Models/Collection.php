<?php

namespace Soyhuce\NextIdeHelper\Domain\Models;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Str;

class Collection
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
        return $this->fqcn === '\\' . EloquentCollection::class;
    }
}
