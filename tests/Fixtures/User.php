<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property \Soyhuce\NextIdeHelper\Tests\Fixtures\Address $address
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostCollection $laravelPosts
 * @property-read \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostCollection $posts
 */
class User extends Model
{
    use HasFactory;

    protected $casts = [
        'address' => Address::class,
        'shipping_address' => AddressCaster::class,
        'name' => Uppercase::class,
        'nullable_name' => Uppercase::class,
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function laravelPosts(): HasMany
    {
        return $this->hasMany(Post::class)
            ->where('title', 'ilike', '%laravel%');
    }

    public function relationThrowingException(): HasMany
    {
        throw new Exception('Operation not supported');
    }

    public function scopeWhereEmailDomain($query, string $domain, ?string $area = null)
    {
        if ($area !== null) {
            $domain = $domain . '.' . $area;
        }

        return $query->where('email', 'like', "%@{$domain}");
    }
}
