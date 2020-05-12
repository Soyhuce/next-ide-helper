<?php

namespace Soyhuce\NextIdeHelper\Tests\Fixtures\Blog;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery whereId(int|string $value)
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery whereTitle(string $value)
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery whereSubtitle(string|null $value)
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery whereContent(string $value)
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery whereUserId(int|string $value)
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery whereCreatedAt(\Illuminate\Support\Carbon|string|null $value)
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostQuery whereUpdatedAt(\Illuminate\Support\Carbon|string|null $value)
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post create(array $attributes = [])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostCollection|\Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post|null find($id, array $columns = ['*'])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostCollection findMany($id, array $columns = ['*'])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostCollection|\Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post findOrFail($id, array $columns = ['*'])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post findOrNew($id, array $columns = ['*'])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post|null first(array|string $columns = ['*'])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post firstOrCreate(array $attributes, array $values = [])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post firstOrFail(array $columns = ['*'])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post firstOrNew(array $attributes = [], array $values = [])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post forceCreate(array $attributes = [])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostCollection get(array|string $columns = ['*'])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post getModel()
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\PostCollection getModels(array|string $columns = ['*'])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post newModelInstance(array $attributes = [])
 * @method \Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post updateOrCreate(array $attributes, array $values = [])
 * @template TModelClass
 * @extends \Illuminate\Database\Eloquent\Builder<\Test\Fixtures\Blog\Post>
 */
class PostQuery extends Builder
{
}
