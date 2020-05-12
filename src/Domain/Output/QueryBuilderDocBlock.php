<?php

namespace Soyhuce\NextIdeHelper\Domain\Output;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Models\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Method;
use Soyhuce\NextIdeHelper\Domain\Models\Model;
use Soyhuce\NextIdeHelper\Domain\Output\HelperFile\IdeHelperFile;

class QueryBuilderDocBlock extends DocBlock
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function render(): void
    {
        if ($this->model->queryBuilder->isBuiltIn()) {
            return;
        }

        $this->replacePreviousBlock(
            $this->docblock(),
            $this->model->queryBuilder->fqcn,
            $this->model->queryBuilder->filePath
        );
    }

    public function docTags(): Collection
    {
        return Collection::make()
            ->merge($this->attributeScopes())
            ->merge($this->scopeMethods())
            ->merge($this->resultMethods())
            ->merge($this->templateBlock());
    }

    private function docblock(): string
    {
        return $this->docTags()
            ->prepend('/**')
            ->push(' */')
            ->map(fn (?string $line): string => $this->line($line))
            ->implode('');
    }

    private function attributeScopes(): Collection
    {
        return $this->model->attributes
            ->onlyReadOnly(false)
            ->map(function (Attribute $attribute): string {
                return sprintf(
                    ' * @method %s where%s(%s $value)',
                    $this->model->queryBuilder->fqcn,
                    Str::studly($attribute->name),
                    $this->attributeScopeValueType($attribute)
                );
            });
    }

    private function attributeScopeValueType(Attribute $attribute): string
    {
        $type = $attribute->type;

        if ($type !== 'string') {
            $type .= '|string';
        }

        if ($attribute->nullable) {
            $type .= '|null';
        }

        return $type;
    }

    private function scopeMethods(): Collection
    {
        return collect($this->model->scopes)
            ->map(function (Method $scope): string {
                return sprintf(
                    ' * @method %s %s(%s)',
                    $this->model->queryBuilder->isBuiltIn()
                        ? IdeHelperFile::eloquentBuilder($this->model->fqcn)
                        : $this->model->queryBuilder->fqcn,
                    $scope->name,
                    implode(', ', $scope->parameters)
                );
            });
    }

    private function resultMethods(): Collection
    {
        $model = $this->model->fqcn;
        $collection = $this->model->collection->fqcn;

        return Collection::make([
            "{$model} create(array \$attributes = [])",
            "{$collection}|{$model}|null find(\$id, array \$columns = ['*'])",
            "{$collection} findMany(\$id, array \$columns = ['*'])",
            "{$collection}|{$model} findOrFail(\$id, array \$columns = ['*'])",
            "{$model} findOrNew(\$id, array \$columns = ['*'])",
            "{$model}|null first(array|string \$columns = ['*'])",
            "{$model} firstOrCreate(array \$attributes, array \$values = [])",
            "{$model} firstOrFail(array \$columns = ['*'])",
            "{$model} firstOrNew(array \$attributes = [], array \$values = [])",
            "{$model} forceCreate(array \$attributes = [])",
            "{$collection} get(array|string \$columns = ['*'])",
            "{$model} getModel()",
            "{$collection} getModels(array|string \$columns = ['*'])",
            "{$model} newModelInstance(array \$attributes = [])",
            "{$model} updateOrCreate(array \$attributes, array \$values = [])",
        ])
            ->map(static fn (string $method) => " * @method {$method}");
    }

    private function templateBlock(): Collection
    {
        return Collection::make([
            ' * @template TModelClass',
            " * @extends \\Illuminate\\Database\\Eloquent\\Builder<{$this->model->fqcn}>",
        ]);
    }
}
