<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures80;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function intOrString(): int | string
    {
        return 'foo';
    }
}
