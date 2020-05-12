<?php

namespace Soyhuce\NextIdeHelper\Domain\Actions;

use ReflectionClass;
use Soyhuce\NextIdeHelper\Domain\Models\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Model;
use Soyhuce\NextIdeHelper\Exceptions\CannotFindClassFile;

class ResolveModelCollection implements ModelResolver
{
    public function execute(Model $model): void
    {
        $class = new ReflectionClass($model->instance()->newCollection());

        $file = $class->getFileName();

        if ($file === null) {
            throw new CannotFindClassFile($class->getName());
        }

        $model->collection = new Collection($class->getName(), $file);
    }
}
