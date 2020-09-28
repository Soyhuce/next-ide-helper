<?php

namespace Soyhuce\NextIdeHelper\Domain\Factories\Entities;

use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

class Factory
{
    public string $fqcn;

    public string $filePath;

    public Model $model;

    /** @var \Illuminate\Support\Collection<string> */
    public Collection $extraMethods;

    private ?EloquentFactory $instance = null;

    public function __construct(string $fqcn, string $filePath)
    {
        $this->fqcn = Str::start($fqcn, '\\');
        $this->filePath = $filePath;
        $this->extraMethods = new Collection();
    }

    public function instance(): EloquentFactory
    {
        return $this->instance ??= new $this->fqcn();
    }

    public function addMethod(string $method): self
    {
        $this->extraMethods->add($method);

        return $this;
    }
}
