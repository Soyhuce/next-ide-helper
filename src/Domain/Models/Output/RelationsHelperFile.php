<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Output;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperClass;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;

class RelationsHelperFile
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function amend(IdeHelperFile $file): void
    {
        /** @var \Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation $relation */
        foreach ($this->model->relations as $relation) {
            if ($relation->eloquentRelation() instanceof MorphTo) {
                continue;
            }

            $fakeRelationClass = IdeHelperClass::relation($this->model->fqcn, $relation->name);

            $file->getOrAddClass($this->model->fqcn)
                ->addMethod(Method::fromMethod(
                    '__construct',
                    (new ReflectionClass($this->model->fqcn))->getConstructor()
                ))
                ->addDocTag(
                    Method::new($relation->name)->returnType($fakeRelationClass)->toDocTag()
                );

            $file->getOrAddClass($fakeRelationClass)
                ->extends(get_class($relation->eloquentRelation()))
                ->addDocTags(Collection::make([
                    " * @mixin {$this->relatedQueryBuilder($relation)}",
                ]));
        }
    }

    private function relatedQueryBuilder(Relation $relation): string
    {
        $related = $relation->related;
        if (!$related->queryBuilder->isBuiltIn()) {
            return $related->queryBuilder->fqcn;
        }

        return IdeHelperClass::eloquentBuilder($related->fqcn);
    }
}
