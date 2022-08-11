<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Support\Type;
use function get_class;

class ApplyAttributeOverrides implements ModelResolver
{
    /**
     * @param array<string, array<string, string>> $overrides
     */
    public function __construct(
        private array $overrides,
    ) {
    }

    public function execute(Model $model): void
    {
        foreach ($this->overridesFor($model) as $name => $type) {
            $attribute = $model->attributes->findByName($name);
            if ($attribute !== null) {
                $attribute->setType($this->formatTypes($type));
                $attribute->nullable = Str::startsWith($type, '?');
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
        ) ?? $this->format($types);
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
