<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Models\Actions;

use Illuminate\Support\Facades\Date;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributes;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\TestCase;
use function get_class;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributes
 */
class ResolveModelAttributeTest extends TestCase
{
    /**
     * @test
     */
    public function attributesAreResolvedFromDatabase(): void
    {
        $model = new Model(User::class, $this->fixturePath('User.php'));

        $resolveAttributes = new ResolveModelAttributes();

        $resolveAttributes->execute($model);

        $this->assertCount(10, $model->attributes);
    }

    /**
     * @test
     */
    public function timestampsAreCorrectlyResolved(): void
    {
        $model = new Model(User::class, $this->fixturePath('User.php'));

        $resolveAttributes = new ResolveModelAttributes();

        $resolveAttributes->execute($model);

        $createdAt = $model->attributes->findByName('created_at');
        $this->assertNotNull($createdAt);
        $this->assertEquals('\\' . Date::now()::class, $createdAt->type);
        $this->assertFalse($createdAt->nullable);
    }
}
