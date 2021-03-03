<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Collections;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Type;

/**
 * @extends \Illuminate\Support\Collection<\Soyhuce\IdeHelper\Domain\Models\Entities\Model>
 */
class ModelCollection extends Collection
{
    public function findByFqcn(string $fqcn): ?Model
    {
        $fqcn = Type::qualify($fqcn);

        return $this->first(static fn (Model $model) => $model->fqcn === $fqcn);
    }
}
