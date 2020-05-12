<?php

namespace Soyhuce\NextIdeHelper\Domain\Actions;

use ReflectionClass;
use Soyhuce\NextIdeHelper\Domain\Models\Model;
use Soyhuce\NextIdeHelper\Domain\Models\QueryBuilder;
use Soyhuce\NextIdeHelper\Exceptions\CannotFindClassFile;

class ResolveModelQueryBuilder implements ModelResolver
{
    public function execute(Model $model): void
    {
        $class = new ReflectionClass($model->instance()->newQuery());

        $file = $class->getFileName();

        if ($file === null) {
            throw new CannotFindClassFile($class->getName());
        }

        $model->queryBuilder = new QueryBuilder($class->getName(), $file);
    }
}
