<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

class Address
{
    private array $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function toArray(): array
    {
        return $this->values;
    }
}
