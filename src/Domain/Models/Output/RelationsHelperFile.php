<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Output;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Contracts\Amender;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperClass;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;
use Soyhuce\NextIdeHelper\Support\Type;

class RelationsHelperFile implements Amender
{
    public function __construct(
        private Model $model,
        private string $modelFqcn,
    ) {}

    public function amend(IdeHelperFile $file): void
    {
        /** @var Relation $relation */
        foreach ($this->model->relations as $relation) {
            if ($relation->eloquentRelation() instanceof MorphTo) {
                continue;
            }

            $fakeRelationClass = IdeHelperClass::relation($this->model->fqcn, $relation->name);

            $model = $file->getOrAddClass($this->modelFqcn)
                ->addDocTag(Method::new($relation->name)->returnType($fakeRelationClass)->toDocTag());

            if ($this->model->fqcn === $this->modelFqcn) {
                $constructor = (new ReflectionClass($this->model->fqcn))->getConstructor();
                if ($constructor !== null) {
                    $model->addMethod(
                        Method::fromMethod('__construct', $constructor)->docblock(null)
                    );
                }
            }

            $file->getOrAddClass($fakeRelationClass)
                ->addDocTags(Collection::make([
                    " * @mixin {$this->relatedQueryBuilder($relation)}",
                    ' * @mixin ' . Type::qualify($relation->eloquentRelation()::class),
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
