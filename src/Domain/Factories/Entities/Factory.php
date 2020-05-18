<?php

namespace Soyhuce\NextIdeHelper\Domain\Factories\Entities;

use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

class Factory
{
    public string $fqcn;

    public string $filePath;

    public Model $model;

    private ?EloquentFactory $instance = null;

    public function __construct(string $fqcn, string $filePath)
    {
        $this->fqcn = Str::start($fqcn, '\\');
        $this->filePath = $filePath;
    }

    public function instance(): EloquentFactory
    {
        return $this->instance ??= new $this->fqcn();
    }
}
