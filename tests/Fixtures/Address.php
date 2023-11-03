<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Illuminate\Contracts\Database\Eloquent\Castable;

class Address implements Castable
{
    public function __construct(
        private array $values,
    ) {}

    public function city(): ?string
    {
        return $this->values['city'] ?? '';
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
