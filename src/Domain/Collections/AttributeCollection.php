<?php

namespace Soyhuce\NextIdeHelper\Domain\Collections;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Attribute;

/**
 * @extends \Illuminate\Support\Collection<\Soyhuce\IdeHelper\Domain\Models\Attribute>
 */
class AttributeCollection extends Collection
{
    public function findByName(string $name): ?Attribute
    {
        return $this->first(static fn (Attribute $attribute) => $attribute->name === $name);
    }

    public function onlyReadOnly(bool $readOnly = true)
    {
        return $this->filter(static fn (Attribute $attribute): bool => $attribute->readOnly === $readOnly);
    }
}
