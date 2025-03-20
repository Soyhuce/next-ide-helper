<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Support\Type;
use function in_array;

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
        $generic = config('next-ide-helper.models.generic_builders', [EloquentBuilder::class]);

        return in_array(
            Str::ltrim($this->fqcn, '\\'),
            $generic,
            true
        );
    }

    public function addExtra(string $extra): self
    {
        $this->extras->add($extra);

        return $this;
    }
}
