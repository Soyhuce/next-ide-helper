<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures\Blog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;

class Post extends Model
{
    use SoftDeletes;

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
