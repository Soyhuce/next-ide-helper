<?php declare(strict_types=1);

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Address;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Comment;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Commentable;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;

beforeEach(function (): void {
    Factory::guessFactoryNamesUsing(
        fn (string $modelFqcn) => 'Soyhuce\\NextIdeHelper\\Tests\\Fixtures\\Factories\\' . class_basename($modelFqcn) . 'Factory'
    );
});

afterEach(function (): void {
    File::delete($this->fixturePath('_ide_models.php'));
    File::deleteDirectory($this->fixturePath('ide_helper'));
});

test('the command is successful', function (): void {
    config([
        'next-ide-helper.models' => [
            'directories' => [$this->fixturePath()],
            'file_name' => $this->fixturePath() . '/_ide_models.php',
            'overrides' => [
                User::class => [
                    'city' => 'string',
                ],
                Post::class => [
                    'likes' => 'int',
                    'address' => '?' . Address::class,
                    'user' => User::class . '|null',
                    'created_at' => CarbonInterface::class,
                    'metas' => '?array<int,string>',
                ],
                Comment::class => [
                    'commentable' => Commentable::class . '&' . Model::class,
                ],
            ],
        ],
    ]);

    $this->artisan('next-ide-helper:models')
        ->assertExitCode(0);

    expect($this->fixtureFile('Blog/Post.php'))->toMatchSnapshot();

    expect($this->fixtureFile('User.php'))->toMatchSnapshot();

    expect($this->fixtureFile('Comment.php'))->toMatchSnapshot();

    expect($this->fixtureFile('Blog/PostQuery.php'))->toMatchSnapshot();

    expect($this->fixtureFile('_ide_models.php'))->toMatchSnapshot();
});

test('the command is successful with larastan friendly comments', function (): void {
    config([
        'next-ide-helper.models' => [
            'directories' => [$this->fixturePath()],
            'file_name' => $this->fixturePath() . '/_ide_models.php',
            'larastan_friendly' => true,
        ],
    ]);

    $this->artisan('next-ide-helper:models')
        ->assertExitCode(0);

    expect($this->fixtureFile('Blog/Post.php'))->toMatchSnapshot();

    expect($this->fixtureFile('Comment.php'))->toMatchSnapshot();

    expect($this->fixtureFile('Blog/PostQuery.php'))->toMatchSnapshot();

    expect($this->fixtureFile('_ide_models.php'))->toMatchSnapshot();
});

test('the command is successful when writing mixins', function (): void {
    config([
        'next-ide-helper.models' => [
            'directories' => [$this->fixturePath()],
            'file_name' => $this->fixturePath() . '/ide_helper/models.php',
            'use_mixin' => true,
            'mixin_attributes' => true,
            'mixin_meta' => $this->fixturePath() . '/ide_helper/.phpstorm.meta.php/models.php',
        ],
    ]);

    $this->artisan('next-ide-helper:models')
        ->assertExitCode(0);

    expect($this->fixtureFile('Blog/Post.php'))->toMatchSnapshot();

    expect($this->fixtureFile('User.php'))->toMatchSnapshot();

    expect($this->fixtureFile('Comment.php'))->toMatchSnapshot();

    expect($this->fixtureFile('Blog/PostQuery.php'))->toMatchSnapshot();

    expect($this->fixtureFile('ide_helper/models.php'))->toMatchSnapshot();

    expect($this->fixtureFile('ide_helper/.phpstorm.meta.php/models.php'))->toMatchSnapshot();
});
