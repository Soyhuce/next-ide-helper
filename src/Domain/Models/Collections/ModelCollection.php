<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Collections;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

/**
 * @extends \Illuminate\Support\Collection<\Soyhuce\IdeHelper\Domain\Models\Entities\Model>
 */
class ModelCollection extends Collection
{
    public function findByFqcn(string $fqcn): ?Model
    {
        return $this->first(static fn (Model $model) => $model->fqcn === Str::start($fqcn, '\\'));
    }
}
