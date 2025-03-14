<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Output;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Contracts\Renderer;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\Output\DocBlock;
use function sprintf;

class QueryBuilderDocBlock extends DocBlock implements Renderer
{
    public function __construct(
        private Model $model,
    ) {}

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

    /**
     * @return Collection<int, string>
     */
    public function docTags(): Collection
    {
        /** @var Collection<int, string> $collection */
        $collection = new Collection();

        return $collection
            ->merge($this->attributeScopes())
            ->merge($this->scopeMethods())
            ->merge($this->resultMethods())
            ->when(
                $this->model->softDeletes(),
                fn (Collection $collection) => $collection->merge($this->softDeletesMethods())
            )
            ->when(
                $this->larastanFriendly(),
                fn (Collection $collection) => $collection
                    ->merge($this->templateBlock())
            )
            ->merge($this->model->queryBuilder->extras);
    }

    private function docblock(): string
    {
        return $this->docTags()
            ->prepend('/**')
            ->push(' */')
            ->map(fn (string $line): string => $this->line($line))
            ->implode('');
    }

    /**
     * @return Collection<int, string>
     */
    private function attributeScopes(): Collection
    {
        return $this->model->attributes
            ->onlyReadOnly(false)
            ->onlyInDatabase(true)
            ->toBase()
            ->map(fn (Attribute $attribute): string => sprintf(
                ' * @method $this where%s(%s $value)',
                Str::studly($attribute->name),
                $this->attributeScopeValueType($attribute)
            ));
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

    /**
     * @return Collection<int, string>
     */
    private function scopeMethods(): Collection
    {
        return collect($this->model->scopes)
            ->flatMap(fn (Method $scope): array => array_filter([
                $scope->returnType('$this')->toDocTag(),
                $scope->toLinkTag(),
            ]));
    }

    /**
     * @return Collection<int, string>
     */
    private function resultMethods(): Collection
    {
        $model = $this->model->fqcn;
        $collection = $this->model->collection->fqcn;

        if ($this->larastanFriendly()) {
            $collection .= "<int, {$this->model->fqcn}>";
        }

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
            "{$model} sole(array|string \$columns = ['*'])",
            "{$model} updateOrCreate(array \$attributes, array \$values = [])",
        ])
            ->map(static fn (string $method) => " * @method {$method}");
    }

    /**
     * @return Collection<int, string>
     */
    private function softDeletesMethods(): Collection
    {
        return Collection::make([
            'int restore()',
            '$this withTrashed(bool $withTrashed = true)',
            '$this withoutTrashed()',
            '$this onlyTrashed()',
        ])
            ->map(static fn (string $method) => " * @method {$method}");
    }

    /**
     * @return Collection<int, string>
     */
    private function templateBlock(): Collection
    {
        return Collection::make([
            " * @template TModelClass of {$this->model->fqcn}",
            ' * @extends \\Illuminate\\Database\\Eloquent\\Builder<TModelClass>',
        ]);
    }

    private function larastanFriendly(): bool
    {
        return (bool) config('next-ide-helper.models.larastan_friendly', false);
    }
}
