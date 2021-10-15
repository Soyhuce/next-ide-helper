<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Type;
use function get_class;

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
                $attribute->setType($this->formatTypes($type));
            }

            $relation = $model->relations->findByName($name);
            if ($relation !== null) {
                $relation->forceReturnType($this->formatTypes($type));
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

    private function formatTypes(string $types): string
    {
        return preg_replace_callback(
            '/[^|&]+/',
            function (array $match) {
                return $this->format($match[0]);
            },
            $types,
        );
    }

    private function format(?string $type): string
    {
        if (!Str::startsWith($type, '?')) {
            return Type::qualify($type);
        }

        $type = Str::after($type, '?');

        return '?' . Type::qualify($type);
    }
}
