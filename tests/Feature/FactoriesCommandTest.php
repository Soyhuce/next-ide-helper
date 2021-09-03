<?php

namespace Soyhuce\NextIdeHelper\Tests\Feature;

use Soyhuce\NextIdeHelper\Tests\ResetsFixtures;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversNothing
 */
class FactoriesCommandTest extends TestCase
{
    use ResetsFixtures;

    /**
     * @test
     */
    public function theCommandIsSuccessful(): void
    {
        config([
            'next-ide-helper.factories' => [
                'directories' => [$this->fixturePath()],
            ],
        ]);

        $this->artisan('next-ide-helper:factories')
            ->assertExitCode(0);

        $this->assertFileEquals(
            $this->expectedPath('PostFactory.stub'),
            $this->fixturePath('Factories/PostFactory.php')
        );

        $this->assertFileEquals(
            $this->expectedPath('UserFactory.stub'),
            $this->fixturePath('Factories/UserFactory.php')
        );

        $this->assertFileEquals(
            $this->expectedPath('CommentFactory.stub'),
            $this->fixturePath('Factories/CommentFactory.php')
        );
    }
}
