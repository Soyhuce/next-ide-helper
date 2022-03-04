<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Models\Actions;

use Illuminate\Database\Eloquent\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostCollection;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversNothing
 */
class ResolveModelCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function itFindsBuiltinCollection(): void
    {
        $model = new Model(User::class, $this->fixturePath('User.php'));

        $resolveModelCollection = new ResolveModelCollection();

        $resolveModelCollection->execute($model);

        $this->assertNotNull($model->collection);

        $this->assertEquals('\\' . Collection::class, $model->collection->fqcn);
        $this->assertTrue($model->collection->isBuiltIn());
    }

    /**
     * @test
     */
    public function itFindsCustomCollection(): void
    {
        $model = new Model(Post::class, $this->fixturePath('Blog/Post.php'));

        $resolveModelCollection = new ResolveModelCollection();

        $resolveModelCollection->execute($model);

        $this->assertNotNull($model->collection);

        $this->assertEquals('\\' . PostCollection::class, $model->collection->fqcn);
        $this->assertEquals($this->fixturePath('Blog/PostCollection.php'), $model->collection->filePath);
        $this->assertFalse($model->collection->isBuiltIn());
    }
}
