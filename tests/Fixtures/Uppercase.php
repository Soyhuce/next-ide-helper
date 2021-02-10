<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Support\Str;

class Uppercase implements CastsInboundAttributes
{
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return Str::upper($value);
    }
}
