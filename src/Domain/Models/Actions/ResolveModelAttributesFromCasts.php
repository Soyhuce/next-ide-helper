<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\AttributeTypeCaster;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

class ResolveModelAttributesFromCasts implements ModelResolver
{
    public function execute(Model $model): void
    {
        $casts = $this->findCasts($model);
        $typeCaster = new AttributeTypeCaster($model);

        foreach ($casts as $name => $cast) {
            if ($model->attributes->findByName($name) !== null) {
                continue;
            }

            $attribute = new Attribute($name, $cast);
            $typeCaster->resolve($attribute);
            $model->addAttribute($attribute);
        }
    }

    /**
     * @return array<string, string>
     */
    private function findCasts(Model $model): array
    {
        return $model->instance()->getCasts();
    }
}
