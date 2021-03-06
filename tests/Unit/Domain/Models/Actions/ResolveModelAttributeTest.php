<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Models\Actions;

use Illuminate\Support\Facades\Date;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributes;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributes
 */
class ResolveModelAttributeTest extends TestCase
{
    /**
     * @test
     */
    public function attributesAreResolvedFromDatabase()
    {
        $model = new Model(User::class, $this->fixturePath('User.php'));

        $resolveAttributes = new ResolveModelAttributes();

        $resolveAttributes->execute($model);

        $this->assertCount(9, $model->attributes);
    }

    /**
     * @test
     */
    public function timestampsAreCorrectlyResolved()
    {
        $model = new Model(User::class, $this->fixturePath('User.php'));

        $resolveAttributes = new ResolveModelAttributes();

        $resolveAttributes->execute($model);

        $createdAt = $model->attributes->findByName('created_at');
        $this->assertNotNull($createdAt);
        $this->assertEquals('\\' . get_class(Date::now()), $createdAt->type);
        $this->assertFalse($createdAt->nullable);
    }
}
