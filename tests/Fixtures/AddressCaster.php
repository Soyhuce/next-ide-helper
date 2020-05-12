<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class AddressCaster implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): Address
    {
        return new Address(json_decode($value, true));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param \Soyhuce\NextIdeHelper\Tests\Fixtures\Address $value
     * @param array $attributes
     * @return array|false|string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value->toArray());
    }
}
