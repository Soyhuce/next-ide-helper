<?php

namespace Soyhuce\NextIdeHelper\Domain\Factories\Output;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Factories\Entities\Factory;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Support\Output\DocBlock;

class FactoryDocBlock extends DocBlock
{
    private Factory $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function render(): void
    {
        $this->replacePreviousBlock(
            $this->docblock(),
            $this->factory->fqcn,
            $this->factory->filePath
        );
    }

    protected function docblock(): string
    {
        return Collection::make([
            '/**',
            $this->creationMethods(),
            $this->relations(),
            ' */',
        ])
            ->map(fn (?string $line): string => $this->line($line))
            ->implode('');
    }

    private function creationMethods(): string
    {
        return collect([
            " * @method {$this->factory->model->fqcn} createOne(\$attributes = [])",
            " * @method {$this->factory->model->fqcn}|{$this->factory->model->collection->fqcn} create(\$attributes = [], \\Illuminate\\Database\\Eloquent\\Model|null \$parent = null)",
            " * @method {$this->factory->model->fqcn} makeOne(\$attributes = [])",
            " * @method {$this->factory->model->fqcn}|{$this->factory->model->collection->fqcn} make(\$attributes = [], \\Illuminate\\Database\\Eloquent\\Model|null \$parent = null)",
            " * @method {$this->factory->model->fqcn} newModel(array \$attributes = [])",
        ])->implode(PHP_EOL);
    }

    private function relations(): ?string
    {
        return $this->factory->model->relations
            ->map(fn (Relation $relation) => $this->relationHelper($relation))
            ->filter()
            ->sort()
            ->implode(PHP_EOL);
    }

    private function relationHelper(Relation $relation): ?string
    {
        if ($relation->eloquentRelation() instanceof BelongsTo) {
            return $this->forRelation($relation);
        }

        if ($relation->eloquentRelation() instanceof HasOneOrMany) {
            return $this->hasRelation($relation);
        }

        if ($relation->eloquentRelation() instanceof BelongsToMany) {
            return $this->hasRelation($relation);
        }

        return null;
    }

    private function forRelation(Relation $relation): ?string
    {
        return sprintf(
            ' * @method %s for%s($attributes = [])',
            $this->factory->fqcn,
            Str::studly($relation->name)
        );
    }

    private function hasRelation(Relation $relation): string
    {
        return sprintf(
            ' * @method %s has%s($count = 1, $attributes = [])',
            $this->factory->fqcn,
            Str::studly($relation->name)
        );
    }
}
