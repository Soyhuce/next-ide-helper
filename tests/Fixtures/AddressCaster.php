<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class AddressCaster implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes): ?Address
    {
        return new Address(json_decode($value, true, flags: JSON_THROW_ON_ERROR));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \Soyhuce\NextIdeHelper\Tests\Fixtures\Address $value
     * @return array|false|string
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value->toArray(), flags: JSON_THROW_ON_ERROR);
    }
}
