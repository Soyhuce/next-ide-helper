<?php declare(strict_types=1);

use Illuminate\Database\Eloquent\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostCollection;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;

it('finds builtin collection', function (): void {
    $model = new Model(User::class, $this->fixturePath('User.php'));

    $resolveModelCollection = new ResolveModelCollection();

    $resolveModelCollection->execute($model);

    expect($model->collection)->not->toBeNull();

    expect($model->collection->fqcn)->toEqual('\\' . Collection::class);
    expect($model->collection->isBuiltIn())->toBeTrue();
});

it('finds custom collection', function (): void {
    $model = new Model(Post::class, $this->fixturePath('Blog/Post.php'));

    $resolveModelCollection = new ResolveModelCollection();

    $resolveModelCollection->execute($model);

    expect($model->collection)->not->toBeNull();

    expect($model->collection->fqcn)->toEqual('\\' . PostCollection::class);
    expect($model->collection->filePath)->toEqual($this->fixturePath('Blog/PostCollection.php'));
    expect($model->collection->isBuiltIn())->toBeFalse();
});
