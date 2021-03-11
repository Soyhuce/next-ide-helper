<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Output;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Support\Output\DocBlock;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperClass;

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
            $this->factory(),
            $this->ideHelperModelMixin(),
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

        $mixin = " * @mixin {$this->model->queryBuilder->fqcn}";

        if (config('next-ide-helper.models.larastan_friendly', false)) {
            $mixin .= "<{$this->model->fqcn}>";
        }

        return $mixin;
    }

    private function factory(): ?string
    {
        if (!trait_exists(\Illuminate\Database\Eloquent\Factories\HasFactory::class)) {
            return null;
        }

        if (!in_array(
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
            class_uses_recursive($this->model->fqcn)
        )) {
            return null;
        }

        $factory = get_class(
            ($this->model->fqcn)::factory()
        );

        return " * @method static \\{$factory} factory(\$count = 1, \$state = [])";
    }

    private function ideHelperModelMixin(): string
    {
        return ' * @mixin ' . IdeHelperClass::model($this->model->fqcn);
    }
}
