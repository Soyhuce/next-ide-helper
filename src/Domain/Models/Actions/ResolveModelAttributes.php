<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\AttributeTypeCaster;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

/**
 * @phpstan-type Column array{name: string, type_name: string, nullable: bool}
 */
class ResolveModelAttributes implements ModelResolver
{
    public function execute(Model $model): void
    {
        $columns = $this->resolveColumns($model);
        $typeCaster = new AttributeTypeCaster($model);

        foreach ($columns as $column) {
            $attribute = new Attribute($column['name'], $column['type_name']);
            $attribute->inDatabase = true;
            if ($column['nullable'] && !$this->isLaravelTimestamp($model, $attribute)) {
                $attribute->nullable = true;
                $attribute->nullableInDatabase = true;
            }

            $model->addAttribute($typeCaster->resolve($attribute));
        }
    }

    /**
     * @return array<Column>
     */
    private function resolveColumns(Model $model): array
    {
        $model->instance()->getTable();

        return $model->instance()
            ->getConnection()
            ->getSchemaBuilder()
            ->getColumns($model->instance()->getTable());
    }

    private function isLaravelTimestamp(Model $model, Attribute $attribute): bool
    {
        if (!$model->instance()->usesTimestamps()) {
            return false;
        }
        if (
            $attribute->name === $model->instance()->getCreatedAtColumn()
            || $attribute->name === $model->instance()->getUpdatedAtColumn()
        ) {
            return true;
        }

        return false;
    }
}
