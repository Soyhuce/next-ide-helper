<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\AttributeCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\RelationCollection;
use Soyhuce\NextIdeHelper\Entities\Method;

class Model
{
    public string $fqcn;

    public string $filePath;

    public AttributeCollection $attributes;

    public QueryBuilder $queryBuilder;

    public Collection $collection;

    public RelationCollection $relations;

    /** @var array<\Soyhuce\NextIdeHelper\Entities\Method> */
    public array $scopes;

    private ?EloquentModel $instance = null;

    public function __construct(string $fqcn, string $filePath)
    {
        $this->fqcn = Str::start($fqcn, '\\');
        $this->filePath = $filePath;
        $this->attributes = new AttributeCollection();
        $this->relations = new RelationCollection();
        $this->scopes = [];
    }

    public function addAttribute(Attribute $attribute): self
    {
        $this->attributes->add($attribute);

        return $this;
    }

    public function addRelation(Relation $relation): self
    {
        $this->relations->add($relation);

        return $this;
    }

    public function addScope(Method $scope): self
    {
        $this->scopes[] = $scope;

        return $this;
    }

    public function instance(): EloquentModel
    {
        return $this->instance ??= new $this->fqcn();
    }

    public function softDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses($this->fqcn));
    }
}
