<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Output;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Contracts\Amender;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\QueryBuilder;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperClass;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;

class QueryBuilderHelperFile implements Amender
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

        $fakeEloquentBuilder = IdeHelperClass::eloquentBuilder($this->model->fqcn);
        $clone = clone $this->model;
        $clone->queryBuilder = new QueryBuilder($fakeEloquentBuilder, '');
        $clone->queryBuilder->extras = $this->model->queryBuilder->extras;
        $queryBuilderDocBlock = new QueryBuilderDocBlock($clone);

        $file->getOrAddClass($fakeEloquentBuilder)
            ->extends(EloquentBuilder::class)
            ->addDocTags($queryBuilderDocBlock->docTags());

        $model = $file->getOrAddClass($this->model->fqcn)
            ->addDocTags(Collection::make([
                " * @method static {$fakeEloquentBuilder} query()",
                " * @mixin {$fakeEloquentBuilder}",
            ]));

        $constructor = (new ReflectionClass($this->model->fqcn))->getConstructor();
        if ($constructor !== null) {
            $model->addMethod(Method::fromMethod('__construct', $constructor));
        }
    }
}
