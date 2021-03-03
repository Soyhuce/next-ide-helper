<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Extensions;

use ReflectionMethod;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Type;

class SpatieModelStateResolver implements ModelResolver
{
    public function execute(Model $model): void
    {
        if (!$this->isModelWithState($model)) {
            return;
        }

        $configs = $this->getStates($model);

        foreach ($configs as $config) {
            $this->setAttributeType(
                $model->attributes->findByName($config->field),
                $config->stateClass
            );
        }
    }

    private function isModelWithState(Model $model): bool
    {
        if (!trait_exists(\Spatie\ModelStates\HasStates::class)) {
            return false;
        }

        if (!in_array(\Spatie\ModelStates\HasStates::class, class_uses_recursive($model->fqcn))) {
            return false;
        }

        return true;
    }

    /**
     * @return array<string, \Spatie\ModelStates\StateConfig>
     */
    private function getStates(Model $model): array
    {
        $method = new ReflectionMethod($model->fqcn, 'getStateConfig');
        $method->setAccessible(true);

        return $method->invoke($model->instance());
    }

    private function setAttributeType(?Attribute $attribute, string $stateClass): void
    {
        if ($attribute === null) {
            return;
        }

        $attribute->type = Type::qualify($stateClass);
    }
}
