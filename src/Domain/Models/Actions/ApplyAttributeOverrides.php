<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Type;
use function get_class;
use function is_array;

class ApplyAttributeOverrides implements ModelResolver
{
    /** @var array<string, array<string, string>> */
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
                $attribute->setType($this->formats($type));
            }

            $relation = $model->relations->findByName($name);
            if ($relation !== null) {
                $relation->forceReturnType($this->formats($type));
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

    private function formats(...$types): string
    {
        if (is_array($types[0])) {
            $types = $types[0];
        }

        return collect($types)
            ->map(fn (string $type) => $this->format($type))
            ->join('|');
    }

    private function format(string $type): string
    {
        if (!Str::startsWith($type, '?')) {
            return Type::qualify($type);
        }

        $type = Str::after($type, '?');

        return '?' . Type::qualify($type);
    }
}
