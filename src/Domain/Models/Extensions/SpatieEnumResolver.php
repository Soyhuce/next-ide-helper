<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Extensions;

use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Type;

class SpatieEnumResolver implements ModelResolver
{
    public function execute(Model $model): void
    {
        if (!$this->isModelWithEnum($model)) {
            return;
        }

        foreach ($model->instance()->enums as $attribute => $enumType) {
            $this->updateAttributeType($model->attributes->findByName($attribute), $enumType);
        }
    }

    private function isModelWithEnum(Model $model): bool
    {
        if (!trait_exists(\Spatie\Enum\Laravel\HasEnums::class)) {
            return false;
        }

        if (!in_array(\Spatie\Enum\Laravel\HasEnums::class, class_uses_recursive($model->fqcn))) {
            return false;
        }

        return true;
    }

    private function updateAttributeType(?Attribute $attribute, string $enumType): void
    {
        if ($attribute === null) {
            return;
        }

        [$type, $options] = $this->parseEnumType($enumType);
        $attribute->type = Type::qualify($type);
        $attribute->nullable = in_array('nullable', $options);
    }

    private function parseEnumType(string $enumType): array
    {
        if (!Str::contains($enumType, ':')) {
            return [$enumType, []];
        }
        [$type, $options] = explode(':', $enumType);

        return [$type, explode(',', $options)];
    }
}
