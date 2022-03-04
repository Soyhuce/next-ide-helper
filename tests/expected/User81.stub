<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Fixtures81;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $address
 * @property \Soyhuce\NextIdeHelper\Tests\Fixtures81\Name $name
 * @property string|null $nullable_name
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class User extends Model
{
    protected $casts = [
        'name' => Name::class,
    ];
}
