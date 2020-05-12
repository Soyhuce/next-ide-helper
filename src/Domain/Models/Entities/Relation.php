<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use ReflectionObject;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

class Relation
{
    public string $name;

    public Model $parent;

    public Model $related;

    public function __construct(string $name, Model $parent, Model $related)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->related = $related;
    }

    public function returnType(): string
    {
        $relation = $this->eloquentRelation();
        $relation->initRelation([$this->parent->instance()], $this->name);
        $defaultValue = $this->parent->instance()->getRelation($this->name);

        if ($defaultValue instanceof EloquentCollection) {
            return $this->relatedCollection();
        }

        if ($defaultValue !== null || !$this->isNullable($relation)) {
            return $this->related->fqcn;
        }

        return "{$this->related->fqcn}|null";
    }

    public function eloquentRelation(): EloquentRelation
    {
        return $this->parent->instance()->{$this->name}();
    }

    private function relatedCollection(): string
    {
        $collection = $this->related->collection;
        if (!$collection->isBuiltIn()) {
            return $collection->fqcn;
        }

        return "{$this->related->collection->fqcn}<{$this->related->fqcn}>";
    }

    private function isNullable(EloquentRelation $relation): bool
    {
        $object = new ReflectionObject($relation);
        if (!$object->hasProperty('foreignKey')) {
            return false;
        }

        $foreignKey = $object->getProperty('foreignKey');
        $foreignKey->setAccessible(true);

        $attribute = $this->parent->attributes->findByName($foreignKey->getValue($relation));
        if ($attribute === null) {
            return false;
        }

        return $attribute->nullable;
    }
}
