<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Soyhuce\NextIdeHelper\Support\Type;

class Collection
{
    public string $fqcn;

    public string $filePath;

    public function __construct(string $fqcn, string $filePath)
    {
        $this->fqcn = Type::qualify($fqcn);
        $this->filePath = $filePath;
    }

    public function isBuiltIn(): bool
    {
        return $this->fqcn === '\\' . EloquentCollection::class;
    }
}
