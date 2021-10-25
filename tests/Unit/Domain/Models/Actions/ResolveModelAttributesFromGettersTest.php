<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Models\Actions;

use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributesFromGetters;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributesFromGetters
 */
class ResolveModelAttributesFromGettersTest extends TestCase
{
    /**
     * @test
     */
    public function itDoesNotResolveAnythingIfModelHasNoGetter(): void
    {
        $model = new Model(User::class, $this->fixturePath('User.php'));

        $resolveAttributes = new ResolveModelAttributesFromGetters();

        $resolveAttributes->execute($model);

        $this->assertCount(0, $model->attributes);
    }

    /**
     * @test
     */
    public function itDoesResolveReadOnlyAttributes(): void
    {
        $model = new Model(Post::class, $this->fixturePath('Blog/Post.php'));

        $resolveAttributes = new ResolveModelAttributesFromGetters();

        $resolveAttributes->execute($model);

        $this->assertCount(2, $model->attributes);

        /** @var \Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute $attribute */
        $attribute = $model->attributes->first();
        $this->assertEquals('slug', $attribute->name);
        $this->assertFalse($attribute->nullable);
        $this->assertTrue($attribute->readOnly);
        $this->assertEquals('string', $attribute->type);
    }
}
