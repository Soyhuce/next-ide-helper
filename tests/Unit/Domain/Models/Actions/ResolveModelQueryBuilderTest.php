<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Models\Actions;

use Illuminate\Database\Eloquent\Builder;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelQueryBuilder;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelQueryBuilder
 */
class ResolveModelQueryBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function it_finds_builtin_builder()
    {
        $model = new Model(User::class, $this->fixturePath('User.php'));

        $resolveQueryBuilder = new ResolveModelQueryBuilder();

        $resolveQueryBuilder->execute($model);

        $this->assertNotNull($model->queryBuilder);

        $this->assertEquals('\\' . Builder::class, $model->queryBuilder->fqcn);
        $this->assertTrue($model->queryBuilder->isBuiltIn());
    }

    /**
     * @test
     */
    public function it_finds_custom_builder()
    {
        $model = new Model(Post::class, $this->fixturePath('Blog/Post.php'));

        $resolveQueryBuilder = new ResolveModelQueryBuilder();

        $resolveQueryBuilder->execute($model);

        $this->assertNotNull($model->queryBuilder);

        $this->assertEquals('\\' . PostQuery::class, $model->queryBuilder->fqcn);
        $this->assertEquals($this->fixturePath('Blog/PostQuery.php'), $model->queryBuilder->filePath);
        $this->assertFalse($model->queryBuilder->isBuiltIn());
    }
}
