<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Factories\Output;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Factories\Entities\Factory;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Support\Output\DocBlock;
use function sprintf;

class FactoryDocBlock extends DocBlock
{
    public function __construct(
        private Factory $factory,
    ) {}

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
            $this->relations(),
            $this->extraMethods(),
            $this->template(),
            ' */',
        ])
            ->map(fn (?string $line): string => $this->line($line))
            ->implode('');
    }

    private function relations(): string
    {
        return $this->factory->model->relations
            ->map(fn (Relation $relation) => $this->relationHelper($relation))
            ->filter()
            ->sort()
            ->implode(PHP_EOL);
    }

    private function relationHelper(Relation $relation): ?string
    {
        if ($relation->eloquentRelation() instanceof MorphTo) {
            return null;
        }

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
        $method = sprintf('for%s', Str::studly($relation->name));

        if (method_exists($this->factory->fqcn, $method)) {
            return null;
        }

        return sprintf(
            ' * @method %s %s($attributes = [])',
            $this->factory->fqcn,
            $method
        );
    }

    private function hasRelation(Relation $relation): ?string
    {
        $method = sprintf('has%s', Str::studly($relation->name));

        if (method_exists($this->factory->fqcn, $method)) {
            return null;
        }

        return sprintf(
            ' * @method %s %s($count = 1, $attributes = [])',
            $this->factory->fqcn,
            $method
        );
    }

    private function extraMethods(): string
    {
        return $this->factory->extraMethods
            ->filter()
            ->sort()
            ->map(fn (string $method) => ' * @method ' . $method)
            ->implode(PHP_EOL);
    }

    private function template(): string
    {
        return sprintf(
            ' * @extends \\Illuminate\\Database\\Eloquent\\Factories\\Factory<%s>',
            $this->factory->model->fqcn
        );
    }
}
