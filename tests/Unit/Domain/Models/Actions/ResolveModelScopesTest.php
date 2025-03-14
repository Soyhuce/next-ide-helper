<?php declare(strict_types=1);

use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelScopes;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;

it('finds scopes', function (): void {
    $model = new Model(User::class, $this->fixturePath('User.php'));

    $resolveModelScope = new ResolveModelScopes();

    $resolveModelScope->execute($model);

    expect($model->scopes)->toHaveCount(1);

    $scope = $model->scopes[0];
    expect($scope->name)->toEqual('whereEmailDomain');
    expect($scope->parameters)->toEqual('string $domain, ?string $area = null');
});

test('model can have no scope', function (): void {
    $model = new Model(Post::class, $this->fixturePath('/Blog/Post.php'));

    $resolveModelScope = new ResolveModelScopes();

    $resolveModelScope->execute($model);

    expect($model->scopes)->toHaveCount(0);
});
