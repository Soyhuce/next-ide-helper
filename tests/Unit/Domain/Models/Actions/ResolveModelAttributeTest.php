<?php declare(strict_types=1);

use Illuminate\Support\Facades\Date;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributes;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;

test('attributes are resolved from database', function (): void {
    $model = new Model(User::class, $this->fixturePath('User.php'));

    $resolveAttributes = new ResolveModelAttributes();

    $resolveAttributes->execute($model);

    expect($model->attributes)->toHaveCount(10);
});

test('timestamps are correctly resolved', function (): void {
    $model = new Model(User::class, $this->fixturePath('User.php'));

    $resolveAttributes = new ResolveModelAttributes();

    $resolveAttributes->execute($model);

    $createdAt = $model->attributes->findByName('created_at');
    expect($createdAt)->not->toBeNull();
    expect($createdAt->type)->toEqual('\\' . Date::now()::class);
    expect($createdAt->nullable)->toBeFalse();
});

test('timestamps nullability can be configured', function (): void {
    config(['next-ide-helper.models.nullable_timestamps' => true]);

    $model = new Model(User::class, $this->fixturePath('User.php'));

    $resolveAttributes = new ResolveModelAttributes();

    $resolveAttributes->execute($model);

    $createdAt = $model->attributes->findByName('created_at');
    expect($createdAt)->not->toBeNull();
    expect($createdAt->type)->toEqual('\\' . Date::now()::class);
    expect($createdAt->nullable)->toBeTrue();
});
