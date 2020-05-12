<?php

namespace Soyhuce\NextIdeHelper\Tests\Unit\Domain\Actions;

use Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelScopes;
use Soyhuce\NextIdeHelper\Domain\Models\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelScopes
 */
class ResolveModelScopesTest extends TestCase
{
    /**
     * @test
     */
    public function it_finds_scopes()
    {
        $model = new Model(User::class, $this->fixturePath('User.php'));

        $resolveModelScope = new ResolveModelScopes();

        $resolveModelScope->execute($model);

        $this->assertCount(1, $model->scopes);

        $scope = $model->scopes[0];
        $this->assertEquals('whereEmailDomain', $scope->name);
        $this->assertEquals([
            'string $domain',
            '?string $area = null',
        ], $scope->parameters);
    }

    /**
     * @test
     */
    public function model_can_have_no_scope()
    {
        $model = new Model(Post::class, $this->fixturePath('/Blog/Post.php'));

        $resolveModelScope = new ResolveModelScopes();

        $resolveModelScope->execute($model);

        $this->assertCount(0, $model->scopes);
    }
}
