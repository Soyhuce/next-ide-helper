<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Extensions;

use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\AttributeTypeCaster;

class VirtualAttributeResolver implements ModelResolver
{
    public function execute(Model $model): void
    {
        if (!$this->hasVirtualAttributes($model)) {
            return;
        }

        $this->resolveVirtualAttributesFromScopes($model);
    }

    private function hasVirtualAttributes(Model $model): bool
    {
        if (!trait_exists(\Soyhuce\VirtualAttributes\HasVirtualAttributes::class)) {
            return false;
        }

        if (!in_array(\Soyhuce\VirtualAttributes\HasVirtualAttributes::class, class_uses_recursive($model->fqcn))) {
            return false;
        }

        return true;
    }

    private function resolveVirtualAttributesFromScopes(Model $model)
    {
        foreach ($model->scopes as $scope) {
            if (Str::startsWith($scope->name, 'with') && $scope->name !== 'withVirtual') {
                $this->addVirtualAttribute(
                    $model,
                    (string) Str::of($scope->name)->after('with')->snake()
                );
            }
        }
    }

    private function addVirtualAttribute(Model $model, string $attribute)
    {
        $attribute = new Attribute($attribute, 'mixed');
        $attribute->nullable = true; // is attribute really nullable ?
        $attribute->readOnly = true;
        $attribute->comment = 'virtual attribute';

        (new AttributeTypeCaster($model))->resolve($attribute);

        $model->addAttribute($attribute);
    }
}
