<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Output;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Contracts\Amender;
use Soyhuce\NextIdeHelper\Contracts\Renderer;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Support\Output\DocBlock;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperClass;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;
use function sprintf;

class ModelDocBlock extends DocBlock implements Amender, Renderer
{
    public const int FULL = 0x0001;
    public const int PROPERTIES = 0x0010;
    public const int MODEL_MIXIN = 0x0100;

    /**
     * @param int-mask-of<self::*> $flags
     */
    public function __construct(
        private Model $model,
        private int $flags,
    ) {}

    public function render(): void
    {
        $this->replacePreviousBlock(
            $this->docblock(),
            $this->model->fqcn,
            $this->model->filePath
        );
    }

    public function amend(IdeHelperFile $file): void
    {
        $fakeModel = IdeHelperClass::model($this->model->fqcn);

        $file->getOrAddClass($fakeModel)
            ->addDocTags($this->docTags());
    }

    /**
     * @return Collection<int, string>
     */
    private function docTags(): Collection
    {
        return Collection::make()
            ->when(
                $this->wants(self::FULL | self::PROPERTIES),
                fn (Collection $collection) => $collection->merge([
                    ...$this->properties(),
                    ...$this->propertiesRead(),
                    ...$this->propertiesWrite(),
                    ...$this->relations(),
                ]),
            )
            ->when(
                $this->wants(self::FULL),
                fn (Collection $collection) => $collection->merge([
                    $this->all(),
                    $this->query(),
                    $this->queryMixin(),
                    $this->factory(),
                ])
            )
            ->when(
                $this->wants(self::MODEL_MIXIN),
                fn (Collection $collection) => $collection->merge([
                    $this->ideModelMixin(),
                ])
            )
            ->filter();
    }

    protected function docblock(): string
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
    private function properties(): Collection
    {
        return $this->model->attributes
            ->onlyReadOnly(false)
            ->onlyWriteOnly(false)
            ->collect()
            ->map(fn (Attribute $attribute) => ' * @property ' . $this->propertyLine($attribute));
    }

    /**
     * @return Collection<int, string>
     */
    private function propertiesRead(): Collection
    {
        return $this->model->attributes
            ->onlyReadOnly(true)
            ->onlyWriteOnly(false)
            ->collect()
            ->map(fn (Attribute $attribute) => ' * @property-read ' . $this->propertyLine($attribute));
    }

    /**
     * @return Collection<int, string>
     */
    private function propertiesWrite(): Collection
    {
        return $this->model->attributes
            ->onlyReadOnly(false)
            ->onlyWriteOnly(true)
            ->collect()
            ->map(fn (Attribute $attribute) => ' * @property-write ' . $this->propertyLine($attribute));
    }

    private function propertyLine(Attribute $attribute): string
    {
        $line = sprintf('%s $%s', $attribute->toUnionType(), $attribute->name);
        if ($attribute->comment !== null) {
            $line .= " {$attribute->comment}";
        }

        return $line;
    }

    /**
     * @return Collection<int, string>
     */
    private function relations(): Collection
    {
        return $this->model->relations
            ->sortBy('name')
            ->collect()
            ->map(fn (Relation $relation) => " * @property-read {$relation->returnType()} \${$relation->name}");
    }

    private function query(): ?string
    {
        if ($this->model->queryBuilder->isBuiltIn()) {
            return null;
        }

        $type = $this->model->queryBuilder->fqcn;
        if ($this->larastanFriendly()) {
            $type .= "<{$this->model->fqcn}>";
        }

        return " * @method static {$type} query()";
    }

    private function all(): ?string
    {
        if ($this->model->collection->isBuiltIn()) {
            return null;
        }

        $type = $this->model->collection->fqcn;
        if ($this->larastanFriendly()) {
            $type .= "<int, {$this->model->fqcn}>";
        }

        return " * @method static {$type} all(array|mixed \$columns = ['*'])";
    }

    private function queryMixin(): ?string
    {
        if ($this->model->queryBuilder->isBuiltIn()) {
            return null;
        }

        $mixin = " * @mixin {$this->model->queryBuilder->fqcn}";

        if ($this->larastanFriendly()) {
            $mixin .= "<{$this->model->fqcn}>";
        }

        return $mixin;
    }

    private function factory(): ?string
    {
        $factory = $this->model->factory();
        if ($factory === null) {
            return null;
        }

        return " * @method static \\{$factory} factory(\$count = 1, \$state = [])";
    }

    private function ideModelMixin(): string
    {
        $ideModel = IdeHelperClass::model($this->model->fqcn);

        return " * @mixin {$ideModel}";
    }

    private function wants(int $flag): bool
    {
        return ($this->flags & $flag) !== 0;
    }

    private function larastanFriendly(): bool
    {
        return (bool) config('next-ide-helper.models.larastan_friendly', false);
    }
}
