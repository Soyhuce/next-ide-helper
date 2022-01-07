<?php

namespace Soyhuce\NextIdeHelper\Domain\Factories\Entities;

use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Type;

class Factory
{
    /** @var class-string<\Illuminate\Database\Eloquent\Factories\Factory> */
    public string $fqcn;

    public string $filePath;

    public Model $model;

    /** @var \Illuminate\Support\Collection<string> */
    public Collection $extraMethods;

    private ?EloquentFactory $instance = null;

    /**
     * @param class-string<\Illuminate\Database\Eloquent\Factories\Factory> $fqcn
     */
    public function __construct(string $fqcn, string $filePath)
    {
        $this->fqcn = Type::qualify($fqcn);
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
