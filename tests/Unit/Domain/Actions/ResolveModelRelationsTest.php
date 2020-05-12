<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Actions;

use Soyhuce\NextIdeHelper\Domain\Actions\FindModels;
use Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelRelations;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelRelations
 */
class ResolveModelRelationsTest extends TestCase
{
    /**
     * @test
     */
    public function it_finds_model_relations()
    {
        $finder = new FindModels();
        $models = $finder->execute($this->fixturePath());
        $post = $models->findByFqcn(Post::class);

        $resolveModelRelation = new ResolveModelRelations($models);

        $resolveModelRelation->execute($post);

        $this->assertCount(1, $post->relations);

        /** @var \Soyhuce\NextIdeHelper\Domain\Models\Relation $user */
        $user = $post->relations->first();
        $this->assertEquals('user', $user->name);
        $this->assertEquals($post, $user->parent);
        $this->assertEquals($models->findByFqcn(User::class), $user->related);
    }

    /**
     * @test
     */
    public function it_finds_model_custom_relations()
    {
        $finder = new FindModels();
        $models = $finder->execute($this->fixturePath());
        $user = $models->findByFqcn(User::class);

        $resolveModelRelation = new ResolveModelRelations($models);

        $resolveModelRelation->execute($user);

        $this->assertCount(2, $user->relations);

        /** @var \Soyhuce\NextIdeHelper\Domain\Models\Relation $posts */
        $posts = $user->relations->findByName('posts');
        $this->assertEquals('posts', $posts->name);
        $this->assertEquals($user, $posts->parent);
        $this->assertEquals($models->findByFqcn(Post::class), $posts->related);

        /** @var \Soyhuce\NextIdeHelper\Domain\Models\Relation $laravelPosts */
        $laravelPosts = $user->relations->findByName('laravelPosts');
        $this->assertEquals('laravelPosts', $laravelPosts->name);
        $this->assertEquals($user, $laravelPosts->parent);
        $this->assertEquals($models->findByFqcn(Post::class), $laravelPosts->related);
    }
}
