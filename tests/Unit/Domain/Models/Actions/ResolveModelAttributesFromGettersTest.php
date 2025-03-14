<?php declare(strict_types=1);

use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributesFromGetters;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Attribute;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;

it('does not resolve anything if model has no getter', function (): void {
    $model = new Model(User::class, $this->fixturePath('User.php'));

    $resolveAttributes = new ResolveModelAttributesFromGetters();

    $resolveAttributes->execute($model);

    expect($model->attributes)->toHaveCount(0);
});

it('does resolve read only attributes', function (): void {
    $model = new Model(Post::class, $this->fixturePath('Blog/Post.php'));

    $resolveAttributes = new ResolveModelAttributesFromGetters();

    $resolveAttributes->execute($model);

    expect($model->attributes)->toHaveCount(2);

    /** @var Attribute $attribute */
    $attribute = $model->attributes->first();
    expect($attribute->name)->toEqual('slug');
    expect($attribute->nullable)->toBeFalse();
    expect($attribute->readOnly)->toBeTrue();
    expect($attribute->type)->toEqual('string');
});
