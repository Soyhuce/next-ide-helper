<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use ReflectionClass;
use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Exceptions\CannotFindClassFile;

class ResolveModelCollection implements ModelResolver
{
    public function execute(Model $model): void
    {
        $class = new ReflectionClass($model->instance()->newCollection());

        $file = $class->getFileName();

        if ($file === false) {
            throw new CannotFindClassFile($class->getName());
        }

        $model->collection = new Collection($class->getName(), $file);
    }
}
