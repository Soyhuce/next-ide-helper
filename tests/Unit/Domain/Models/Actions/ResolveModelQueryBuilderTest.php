<?php declare(strict_types=1);

use Illuminate\Database\Eloquent\Builder;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelQueryBuilder;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;

it('finds builtin builder', function (): void {
    $model = new Model(User::class, $this->fixturePath('User.php'));

    $resolveQueryBuilder = new ResolveModelQueryBuilder();

    $resolveQueryBuilder->execute($model);

    expect($model->queryBuilder)->not->toBeNull();

    expect($model->queryBuilder->fqcn)->toEqual('\\' . Builder::class);
    expect($model->queryBuilder->isBuiltIn())->toBeTrue();
});

it('finds custom builder', function (): void {
    $model = new Model(Post::class, $this->fixturePath('Blog/Post.php'));

    $resolveQueryBuilder = new ResolveModelQueryBuilder();

    $resolveQueryBuilder->execute($model);

    expect($model->queryBuilder)->not->toBeNull();

    expect($model->queryBuilder->fqcn)->toEqual('\\' . PostQuery::class);
    expect($model->queryBuilder->filePath)->toEqual($this->fixturePath('Blog/PostQuery.php'));
    expect($model->queryBuilder->isBuiltIn())->toBeFalse();
});
