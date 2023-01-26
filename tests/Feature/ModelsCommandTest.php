<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Feature;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Address;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Blog\Post;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Comment;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Commentable;
use Soyhuce\NextIdeHelper\Tests\Fixtures\User;
use Soyhuce\NextIdeHelper\Tests\ResetsFixtures;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Console\ModelsCommand
 */
class ModelsCommandTest extends TestCase
{
    use ResetsFixtures;

    protected function setUp(): void
    {
        parent::setUp();
        Factory::guessFactoryNamesUsing(function (string $modelFqcn) {
            return 'Soyhuce\NextIdeHelper\Tests\Fixtures\Factories\\' . class_basename($modelFqcn) . 'Factory';
        });
    }

    /**
     * @test
     */
    public function theCommandIsSuccessful(): void
    {
        config([
            'next-ide-helper.models' => [
                'directories' => [$this->fixturePath()],
                'file_name' => $this->fixturePath() . '/_ide_models.php',
                'overrides' => [
                    User::class => [
                        'city' => 'string',
                        'birthday' => CarbonInterface::class,
                    ],
                    Post::class => [
                        'likes' => 'int',
                        'address' => '?' . Address::class,
                        'user' => User::class . '|null',
                        'created_at' => CarbonInterface::class,
                    ],
                    Comment::class => [
                        'commentable' => Commentable::class . '&' . Model::class,
                    ],
                ],
            ],
        ]);

        $this->artisan('next-ide-helper:models')
            ->assertExitCode(0);

        $this->assertFileEquals(
            $this->expectedPath('Post.stub'),
            $this->fixturePath('Blog/Post.php')
        );

        $this->assertFileEquals(
            $this->expectedPath('User.stub'),
            $this->fixturePath('User.php')
        );

        $this->assertFileEquals(
            $this->expectedPath('Comment.stub'),
            $this->fixturePath('Comment.php')
        );

        $this->assertFileEquals(
            $this->expectedPath('PostQuery.stub'),
            $this->fixturePath('Blog/PostQuery.php')
        );

        $this->assertFileEquals(
            $this->expectedPath('_ide_models.stub'),
            $this->fixturePath('_ide_models.php')
        );
    }

    /**
     * @test
     */
    public function theCommandIsSuccessfulWithLarastanFriendlyComments(): void
    {
        config([
            'next-ide-helper.models' => [
                'directories' => [$this->fixturePath()],
                'file_name' => $this->fixturePath() . '/_ide_models.php',
                'larastan_friendly' => true,
            ],
        ]);

        $this->artisan('next-ide-helper:models')
            ->assertExitCode(0);

        $this->assertFileEquals(
            $this->expectedPath('PostLarastan.stub'),
            $this->fixturePath('Blog/Post.php')
        );

        $this->assertFileEquals(
            $this->expectedPath('CommentLarastan.stub'),
            $this->fixturePath('Comment.php')
        );

        $this->assertFileEquals(
            $this->expectedPath('PostQueryLarastan.stub'),
            $this->fixturePath('Blog/PostQuery.php')
        );

        $this->assertFileEquals(
            $this->expectedPath('_ide_modelsLarastan.stub'),
            $this->fixturePath('_ide_models.php')
        );
    }

    protected function tearDown(): void
    {
        File::delete($this->fixturePath('_ide_models.php'));
        parent::tearDown();
    }
}
