<?php

namespace Soyhuce\NextIdeHelper\Domain\Output;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Model;
use Soyhuce\NextIdeHelper\Domain\Models\QueryBuilder;
use Soyhuce\NextIdeHelper\Domain\Output\HelperFile\IdeHelperFile;

class QueryBuilderHelperFile
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function amend(IdeHelperFile $file): void
    {
        if (!$this->model->queryBuilder->isBuiltIn()) {
            return;
        }

        $fakeEloquentBuilder = IdeHelperFile::eloquentBuilder($this->model->fqcn);
        $clone = clone $this->model;
        $clone->queryBuilder = new QueryBuilder($fakeEloquentBuilder, '');
        $queryBuilderDocBlock = new QueryBuilderDocBlock($clone);

        $file->getOrAddClass(IdeHelperFile::eloquentBuilder($this->model->fqcn))
            ->extends(EloquentBuilder::class)
            ->addDocTags($queryBuilderDocBlock->docTags());

        $file->getOrAddClass($this->model->fqcn)
            ->addDocTags(Collection::make([
                " * @method {$fakeEloquentBuilder} query()",
                " * @mixin {$fakeEloquentBuilder}",
            ]));
    }
}
