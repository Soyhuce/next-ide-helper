<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Illuminate\Contracts\Database\Eloquent\Castable;

class Address implements Castable
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

    public static function castUsing($arguments = []): string
    {
        return AddressCaster::class;
    }
}
