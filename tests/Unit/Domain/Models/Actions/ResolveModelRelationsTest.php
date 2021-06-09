<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Models\Actions;

use Soyhuce\NextIdeHelper\Domain\Models\Actions\FindModels;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelRelations;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\Fixtures80\User as User80;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelRelations
 */
class ResolveModelRelationsTest extends TestCase
{
    /**
     * @test
     */
    public function itFindsModelRelations()
    {
        $finder = new FindModels();
        $models = $finder->execute($this->fixturePath());
        $post = $models->findByFqcn(Post::class);

        $resolveModelRelation = new ResolveModelRelations($models);

        $resolveModelRelation->execute($post);

        $this->assertCount(2, $post->relations);

        /** @var \Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation $user */
        $user = $post->relations->first(function (Relation $relation) {
            return $relation->name === 'user';
        });
        $this->assertNotNull($user);
        $this->assertEquals($post, $user->parent);
        $this->assertEquals($models->findByFqcn(User::class), $user->related);
    }

    /**
     * @test
     */
    public function itFindsModelCustomRelations()
    {
        $finder = new FindModels();
        $models = $finder->execute($this->fixturePath());
        $user = $models->findByFqcn(User::class);

        $resolveModelRelation = new ResolveModelRelations($models);

        $resolveModelRelation->execute($user);

        $this->assertCount(2, $user->relations);

        /** @var \Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation $posts */
        $posts = $user->relations->findByName('posts');
        $this->assertEquals('posts', $posts->name);
        $this->assertEquals($user, $posts->parent);
        $this->assertEquals($models->findByFqcn(Post::class), $posts->related);

        /** @var \Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation $laravelPosts */
        $laravelPosts = $user->relations->findByName('laravelPosts');
        $this->assertEquals('laravelPosts', $laravelPosts->name);
        $this->assertEquals($user, $laravelPosts->parent);
        $this->assertEquals($models->findByFqcn(Post::class), $laravelPosts->related);
    }

    /**
     * @test
     */
    public function itDoesNotFailWhenModelHaveMethodWithUnionTypeReturn()
    {
        $this->onlyForPhp80();

        $finder = new FindModels();
        $models = $finder->execute($this->fixture80Path());
        $model = $models->findByFqcn(User80::class);

        $resolveModelRelation = new ResolveModelRelations($models);

        $resolveModelRelation->execute($model);

        $this->assertCount(0, $model->relations);
    }
}
