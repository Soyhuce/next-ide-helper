<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Output;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Support\Output\DocBlock;

class ModelDocBlock extends DocBlock
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function render(): void
    {
        $this->replacePreviousBlock(
            $this->docblock(),
            $this->model->fqcn,
            $this->model->filePath
        );
    }

    protected function docblock(): string
    {
        return Collection::make([
            '/**',
            $this->properties(),
            $this->propertiesRead(),
            $this->relations(),
            $this->all(),
            $this->query(),
            $this->queryMixin(),
            ' */',
        ])
            ->map(fn (?string $line): string => $this->line($line))
            ->implode('');
    }

    private function properties(): ?string
    {
        return $this->model->attributes
            ->onlyReadOnly(false)
            ->map(fn (Attribute $attribute) => ' * @property ' . $this->propertyLine($attribute))
            ->implode(PHP_EOL);
    }

    private function propertiesRead(): ?string
    {
        return $this->model->attributes
            ->onlyReadOnly(true)
            ->map(fn (Attribute $attribute) => ' * @property-read ' . $this->propertyLine($attribute))
            ->implode(PHP_EOL);
    }

    private function propertyLine(Attribute $attribute): string
    {
        $line = sprintf('%s $%s', $attribute->toUnionType(), $attribute->name);
        if ($attribute->comment !== null) {
            $line .= " {$attribute->comment}";
        }

        return $line;
    }

    private function relations(): ?string
    {
        return $this->model->relations
            ->sortBy('name')
            ->map(static fn (Relation $relation) => " * @property-read {$relation->returnType()} \${$relation->name}")
            ->implode(PHP_EOL);
    }

    private function query(): ?string
    {
        if ($this->model->queryBuilder->isBuiltIn()) {
            return null;
        }

        return " * @method static {$this->model->queryBuilder->fqcn} query()";
    }

    private function all(): ?string
    {
        if ($this->model->collection->isBuiltIn()) {
            return null;
        }

        return " * @method static {$this->model->collection->fqcn} all(array|mixed \$columns = ['*'])";
    }

    private function queryMixin(): ?string
    {
        if ($this->model->queryBuilder->isBuiltIn()) {
            return null;
        }

        return " * @mixin {$this->model->queryBuilder->fqcn}";
    }
}
