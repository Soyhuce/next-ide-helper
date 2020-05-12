<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use ReflectionClass;
use ReflectionMethod;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\ModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Support\Reflection\FunctionReflection;

class ResolveModelRelations implements ModelResolver
{
    private ModelCollection $models;

    private array $relationsMethods = [
        'hasOne',
        'hasOneThrough',
        'morphOne',
        'belongsTo',
        'hasMany',
        'hasManyThrough',
        'morphMany',
        'belongsToMany',
        'morphTo',
        'morphToMany',
        'morphedByMany',
    ];

    public function __construct(ModelCollection $models)
    {
        $this->models = $models;
    }

    public function execute(Model $model): void
    {
        $methods = $this->findRelationMethods($model);

        foreach ($methods as $method) {
            $model->addRelation(new Relation(
                $method,
                $model,
                $this->findRelatedFromRelation($model, $method)
            ));
        }
    }

    /**
     * @return array<string>
     */
    private function findRelationMethods(Model $model): array
    {
        return collect((new ReflectionClass($model->fqcn))->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(fn (ReflectionMethod $method): bool => !$method->isStatic())
            ->filter(fn (ReflectionMethod $method): bool => $this->isRelationMethod($method))
            ->map(static fn (ReflectionMethod $method): string => $method->getName())
            ->all();
    }

    private function isRelationMethod(ReflectionMethod $method): bool
    {
        if (in_array($method->getName(), $this->relationsMethods)) {
            return false;
        }

        $returnType = FunctionReflection::returnType($method);
        if ($returnType === null) {
            return false;
        }

        return is_subclass_of($returnType, EloquentRelation::class);
    }

    private function findRelatedFromRelation(Model $model, string $method): Model
    {
        /** @var EloquentRelation */
        $relation = $model->instance()->{$method}();
        $relatedClass = get_class($relation->getRelated());

        $related = $this->models->findByFqcn($relatedClass);
        if ($related === null) {
            $related = $this->resolveOutsideModel($relatedClass);
        }

        return $related;
    }

    private function resolveOutsideModel(string $class): Model
    {
        $model = new Model($class, '/dev/null');

        $resolvers = [
            new ResolveModelCollection(),
            new ResolveModelQueryBuilder(),
        ];

        foreach ($resolvers as $resolver) {
            $resolver->execute($model);
        }

        return $model;
    }
}
