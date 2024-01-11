<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Models\Actions;

use Soyhuce\NextIdeHelper\Domain\Models\Actions\FindModels;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelRelations;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelRelations
 */
class ResolveModelRelationsTest extends TestCase
{
    /**
     * @test
     */
    public function itFindsModelRelations(): void
    {
        $finder = new FindModels();
        $models = $finder->execute($this->fixturePath());
        $post = $models->findByFqcn(Post::class);

        $resolveModelRelation = new ResolveModelRelations($models);

        $resolveModelRelation->execute($post);

        $this->assertCount(2, $post->relations);

        /** @var Relation $user */
        $user = $post->relations->first(fn (Relation $relation) => $relation->name === 'user');
        $this->assertNotNull($user);
        $this->assertEquals($post, $user->parent);
        $this->assertEquals($models->findByFqcn(User::class), $user->related);
    }

    /**
     * @test
     */
    public function itFindsModelCustomRelations(): void
    {
        $finder = new FindModels();
        $models = $finder->execute($this->fixturePath());
        $user = $models->findByFqcn(User::class);

        $resolveModelRelation = new ResolveModelRelations($models);

        $resolveModelRelation->execute($user);

        $this->assertCount(3, $user->relations);

        /** @var Relation $posts */
        $posts = $user->relations->findByName('posts');
        $this->assertEquals('posts', $posts->name);
        $this->assertEquals($user, $posts->parent);
        $this->assertEquals($models->findByFqcn(Post::class), $posts->related);

        /** @var Relation $laravelPosts */
        $laravelPosts = $user->relations->findByName('laravelPosts');
        $this->assertEquals('laravelPosts', $laravelPosts->name);
        $this->assertEquals($user, $laravelPosts->parent);
        $this->assertEquals($models->findByFqcn(Post::class), $laravelPosts->related);
    }
}
