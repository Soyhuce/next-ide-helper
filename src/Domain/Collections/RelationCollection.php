<?php

namespace Soyhuce\NextIdeHelper\Domain\Collections;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Relation;

/**
 * @extends \Illuminate\Support\Collection<\Soyhuce\IdeHelper\Domain\Models\Relation>
 */
class RelationCollection extends Collection
{
    public function findByName(string $name): ?Relation
    {
        return $this->first(static fn (Relation $relation) => $relation->name === $name);
    }
}
