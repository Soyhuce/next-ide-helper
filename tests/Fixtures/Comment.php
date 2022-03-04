<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    protected $casts = [
        'datetime' => 'datetime:Y-m-d H:i:s',
        'date' => 'date:Y-m-d',
        'encrypted_array' => 'encrypted:array',
        'encrypted' => 'encrypted',
    ];

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }
}
