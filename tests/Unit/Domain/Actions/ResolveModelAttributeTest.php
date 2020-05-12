<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Actions;

use Illuminate\Support\Facades\Date;
use Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelAttributes;
use Soyhuce\NextIdeHelper\Domain\Models\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelAttributes
 */
class ResolveModelAttributeTest extends TestCase
{
    /**
     * @test
     */
    public function attributes_are_resolved_from_database()
    {
        $model = new Model(User::class, $this->fixturePath('User.php'));

        $resolveAttributes = new ResolveModelAttributes();

        $resolveAttributes->execute($model);

        $this->assertCount(7, $model->attributes);
    }

    /**
     * @test
     */
    public function timestamps_are_correctly_resolved()
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
