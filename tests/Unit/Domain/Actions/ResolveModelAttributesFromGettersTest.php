<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Actions;

use Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelAttributesFromGetters;
use Soyhuce\NextIdeHelper\Domain\Models\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelAttributesFromGetters
 */
class ResolveModelAttributesFromGettersTest extends TestCase
{
    /**
     * @test
     */
    public function it_does_not_resolve_anything_if_model_has_no_getter()
    {
        $model = new Model(User::class, $this->fixturePath('User.php'));

        $resolveAttributes = new ResolveModelAttributesFromGetters();

        $resolveAttributes->execute($model);

        $this->assertCount(0, $model->attributes);
    }

    /**
     * @test
     */
    public function it_does_resolve_read_only_attributes()
    {
        $model = new Model(Post::class, $this->fixturePath('Blog/Post.php'));

        $resolveAttributes = new ResolveModelAttributesFromGetters();

        $resolveAttributes->execute($model);

        $this->assertCount(1, $model->attributes);

        /** @var \Soyhuce\NextIdeHelper\Domain\Models\Attribute $attribute */
        $attribute = $model->attributes->first();
        $this->assertEquals('slug', $attribute->name);
        $this->assertFalse($attribute->nullable);
        $this->assertTrue($attribute->readOnly);
        $this->assertEquals('string', $attribute->type);
    }
}
