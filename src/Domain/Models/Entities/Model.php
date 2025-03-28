<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\AttributeCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\RelationCollection;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\Type;
use Throwable;
use function in_array;

class Model
{
    /** @var class-string<EloquentModel> */
    public string $fqcn;

    public string $filePath;

    public AttributeCollection $attributes;

    public QueryBuilder $queryBuilder;

    public Collection $collection;

    public RelationCollection $relations;

    /** @var array<Method> */
    public array $scopes = [];

    private ?EloquentModel $instance = null;

    /**
     * @param class-string<EloquentModel> $fqcn
     */
    public function __construct(string $fqcn, string $filePath)
    {
        $this->fqcn = Type::qualify($fqcn);
        $this->filePath = $filePath;
        $this->attributes = new AttributeCollection();
        $this->relations = new RelationCollection();
    }

    public function addAttribute(Attribute $attribute): self
    {
        $this->attributes->add($attribute);

        return $this;
    }

    public function addRelation(Relation $relation): self
    {
        $this->relations->add($relation);

        return $this;
    }

    public function addScope(Method $scope): self
    {
        $this->scopes[] = $scope;

        return $this;
    }

    public function instance(): EloquentModel
    {
        return $this->instance ??= new $this->fqcn();
    }

    public function softDeletes(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->fqcn), true);
    }

    public function factory(): ?string
    {
        if (!in_array(
            HasFactory::class,
            class_uses_recursive($this->fqcn),
            true
        )) {
            return null;
        }

        try {
            return $this->fqcn::factory()::class;
        } catch (Throwable) {
            return null;
        }
    }
}
