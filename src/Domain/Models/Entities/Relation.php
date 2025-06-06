<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Support\Str;
use ReflectionObject;

class Relation
{
    public ?string $returnType = null;

    public function __construct(
        public string $name,
        public Model $parent,
        public Model $related,
    ) {}

    public function returnType(): string
    {
        if ($this->returnType !== null) {
            return $this->returnType;
        }

        if ($this->eloquentRelation() instanceof MorphTo) {
            return 'mixed';
        }

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

    public function forceReturnType(string $returnType): void
    {
        if (Str::startsWith($returnType, '?')) {
            $returnType = Str::after($returnType, '?') . '|null';
        }
        $this->returnType = $returnType;
    }

    /**
     * @return EloquentRelation<EloquentModel, EloquentModel, mixed>
     */
    public function eloquentRelation(): EloquentRelation
    {
        return $this->parent->instance()->{$this->name}();
    }

    private function relatedCollection(): string
    {
        return "{$this->related->collection->fqcn}<int, {$this->related->fqcn}>";
    }

    /**
     * @param EloquentRelation<EloquentModel, EloquentModel, mixed> $relation
     */
    private function isNullable(EloquentRelation $relation): bool
    {
        if ($relation instanceof HasOne) {
            return true;
        }

        $object = new ReflectionObject($relation);
        if (!$object->hasProperty('foreignKey')) {
            return false;
        }

        $foreignKey = $object->getProperty('foreignKey');

        $attribute = $this->parent->attributes->findByName($foreignKey->getValue($relation));
        if ($attribute === null) {
            return false;
        }

        return $attribute->nullable;
    }
}
