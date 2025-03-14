<?php declare(strict_types=1);

use Soyhuce\NextIdeHelper\Domain\Models\Actions\FindModels;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

it('finds all models', function (): void {
    $finder = new FindModels();

    $models = $finder->execute($this->fixturePath());

    expect($models)->toHaveCount(3);
});

test('model name and paths are correct', function (): void {
    $finder = new FindModels();

    /** @var Model $model */
    $model = $finder->execute($this->fixturePath('Blog'))->first();

    expect($model->fqcn)->toEqual('\\Soyhuce\\NextIdeHelper\\Tests\\Fixtures\\Blog\\Post');
    expect($model->filePath)->toEqual($this->fixturePath('Blog/Post.php'));
});
