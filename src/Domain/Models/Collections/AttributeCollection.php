<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Collections;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;

/**
 * @extends \Illuminate\Support\Collection<int, \Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute>
 */
class AttributeCollection extends Collection
{
    public function findByName(string $name): ?Attribute
    {
        return $this->first(static fn (Attribute $attribute) => $attribute->name === $name);
    }

    public function onlyReadOnly(bool $readOnly = true): static
    {
        return $this->filter(static fn (Attribute $attribute): bool => $attribute->readOnly === $readOnly);
    }

    public function onlyWriteOnly(bool $writeOnly = true): static
    {
        return $this->filter(static fn (Attribute $attribute): bool => $attribute->writeOnly === $writeOnly);
    }

    public function onlyInDatabase(bool $inDatabase): static
    {
        return $this->filter(static fn (Attribute $attribute): bool => $attribute->inDatabase === $inDatabase);
    }
}
