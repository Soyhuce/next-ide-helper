<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures\Blog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;

/**
 * @property int $id
 * @property string $title
 * @property string|null $subtitle
 * @property string $content
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $slug
 * @property-read \Soyhuce\NextIdeHelper\Tests\Fixtures\User $user
 * @method static \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostCollection all(array|mixed $columns = ['*'])
 * @method static \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery query()
 * @mixin \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery
 */
class Post extends Model
{
    public function newEloquentBuilder($query)
    {
        return new PostQuery($query);
    }

    public function newCollection(array $models = [])
    {
        return new PostCollection($models);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title);
    }
}
