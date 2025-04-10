<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Exception;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;

/**
 * This model is used for testing purposes.
 * @generated
 */
class User extends Model
{
    use HasFactory;

    protected $casts = [
        'address' => Address::class,
        'shipping_address' => AddressCaster::class,
        'name' => Uppercase::class,
        'nullable_name' => Uppercase::class,
        'role' => Role::class,
        'password' => 'hashed',
        'updated_at' => 'immutable_datetime',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function latestPost(): HasOne
    {
        return $this->hasOne(Post::class)->latestOfMany();
    }

    protected function city(): Attribute
    {
        return new Attribute(
            get: fn (): ?string => $this->address->city()
        );
    }

    public function screamedEmail(): Attribute
    {
        return new Attribute(
            get: fn (): string => Str::upper($this->email),
            set: fn (string $value): array => ['email' => Str::lower($value)]
        );
    }

    public function newPassword(): Attribute
    {
        return new Attribute(
            set: fn (string $value): array => ['password' => Hash::make($value)]
        );
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

    #[Scope]
    protected function whereIsAuthor($query): void
    {
        $query->whereHas('posts');
    }

    public function intOrString(): int|string
    {
        return 'foo';
    }
}
