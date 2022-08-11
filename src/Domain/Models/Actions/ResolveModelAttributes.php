<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Doctrine\DBAL\Types\Type;
use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\AttributeTypeCaster;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Exceptions\CannotConnectDatabase;

class ResolveModelAttributes implements ModelResolver
{
    public function execute(Model $model): void
    {
        $columns = $this->resolveColumns($model);
        $typeCaster = new AttributeTypeCaster($model);

        foreach ($columns as $column) {
            $attribute = new Attribute($column->getName(), Type::getTypeRegistry()->lookupName($column->getType()));
            $attribute->inDatabase = true;
            if (!$column->getNotnull() && !$this->isLaravelTimestamp($model, $attribute)) {
                $attribute->nullable = true;
            }

            $model->addAttribute($typeCaster->resolve($attribute));
        }
    }

    /**
     * @return array<\Doctrine\DBAL\Schema\Column>
     */
    private function resolveColumns(Model $model): array
    {
        $table = $model->instance()->getTable();

        $columns = $model->instance()
            ->getConnection()
            ->getDoctrineSchemaManager()
            ->listTableColumns($table);

        if (!$columns) {
            throw new CannotConnectDatabase($table, $model->instance()->getConnectionName());
        }

        return $columns;
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
