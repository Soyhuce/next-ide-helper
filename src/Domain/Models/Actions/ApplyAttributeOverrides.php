<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

class ApplyAttributeOverrides implements ModelResolver
{
    /**
     * @var array<string, array<string, string>>
     */
    private array $overrides;

    public function __construct(array $overrides)
    {
        $this->overrides = $overrides;
    }

    public function execute(Model $model): void
    {
        foreach ($this->overridesFor($model) as $name => $type) {
            $attribute = $model->attributes->findByName($name);
            if ($attribute !== null) {
                $attribute->setType($this->format($type));
            }

            $relation = $model->relations->findByName($name);
            if ($relation !== null) {
                $relation->forceReturnType($this->format($type));
            }
        }
    }

    /**
     * @return array<string, string>
     */
    private function overridesFor(Model $model): array
    {
        return data_get($this->overrides, get_class($model->instance()), []);
    }

    private function format(string $type): string
    {
        $prefix = '';
        if (Str::startsWith($type, '?')) {
            $prefix .= '?';
        }

        $type = Str::after($type, '?');
        if (class_exists($type)) {
            $prefix .= '\\';
        }

        return $prefix . $type;
    }
}
