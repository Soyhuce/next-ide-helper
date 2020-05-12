<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Output;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;
use Soyhuce\NextIdeHelper\Entities\PendingMethod;

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
            $fakeRelationClass = IdeHelperFile::relation($this->model->fqcn, $relation->name);

            $file->getOrAddClass($this->model->fqcn)
                ->addMethod(
                    PendingMethod::new($relation->name)->returns($fakeRelationClass)
                );

            $file->getOrAddClass($fakeRelationClass)
                ->extends(get_class($relation->eloquentRelation()))
                ->addDocTags(Collection::make([
                    " * @mixin {$relation->related->fqcn}",
                ]));
        }
    }
}
