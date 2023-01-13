<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Collections;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;

/**
 * @extends \Illuminate\Support\Collection<int, \Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation>
 */
class RelationCollection extends Collection
{
    public function findByName(string $name): ?Relation
    {
        return $this->first(static fn (Relation $relation) => $relation->name === $name);
    }

    public function removeByName(string $name): void
    {
        $this->each(function (Relation $relation, int $key) use ($name): void {
            if ($relation->name === $name) {
                $this->forget($key);
            }
        });
    }
}
