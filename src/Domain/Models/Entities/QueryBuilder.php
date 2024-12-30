<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Support\Type;

class QueryBuilder
{
    /** @var class-string<EloquentBuilder<EloquentModel>> */
    public string $fqcn;

    public string $filePath;

    /** @var Collection<int, string> */
    public Collection $extras;

    /**
     * @param class-string<EloquentBuilder<EloquentModel>> $fqcn
     */
    public function __construct(string $fqcn, string $filePath)
    {
        $this->fqcn = Type::qualify($fqcn);
        $this->filePath = $filePath;
        $this->extras = new Collection();
    }

    public function isBuiltIn(): bool
    {
        return $this->fqcn === '\\' . EloquentBuilder::class;
    }

    public function addExtra(string $extra): self
    {
        $this->extras->add($extra);

        return $this;
    }
}
