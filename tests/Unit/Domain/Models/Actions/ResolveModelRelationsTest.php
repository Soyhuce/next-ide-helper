<?php declare(strict_types=1);

use Soyhuce\NextIdeHelper\Domain\Models\Actions\FindModels;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelRelations;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Relation;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;

it('finds model relations', function (): void {
    $finder = new FindModels();
    $models = $finder->execute($this->fixturePath());
    $post = $models->findByFqcn(Post::class);

    $resolveModelRelation = new ResolveModelRelations($models);

    $resolveModelRelation->execute($post);

    expect($post->relations)->toHaveCount(2);

    /** @var Relation $user */
    $user = $post->relations->first(fn (Relation $relation) => $relation->name === 'user');
    expect($user)->not->toBeNull();
    expect($user->parent)->toEqual($post);
    expect($user->related)->toEqual($models->findByFqcn(User::class));
});

it('finds model custom relations', function (): void {
    $finder = new FindModels();
    $models = $finder->execute($this->fixturePath());
    $user = $models->findByFqcn(User::class);

    $resolveModelRelation = new ResolveModelRelations($models);

    $resolveModelRelation->execute($user);

    expect($user->relations)->toHaveCount(3);

    /** @var Relation $posts */
    $posts = $user->relations->findByName('posts');
    expect($posts->name)->toEqual('posts');
    expect($posts->parent)->toEqual($user);
    expect($posts->related)->toEqual($models->findByFqcn(Post::class));

    /** @var Relation $laravelPosts */
    $laravelPosts = $user->relations->findByName('laravelPosts');
    expect($laravelPosts->name)->toEqual('laravelPosts');
    expect($laravelPosts->parent)->toEqual($user);
    expect($laravelPosts->related)->toEqual($models->findByFqcn(Post::class));
});
