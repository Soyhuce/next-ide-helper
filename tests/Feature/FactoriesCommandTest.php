<?php

namespace Soyhuce\NextIdeHelper\Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Tests\Fixtures\SomeMacroable;
use Soyhuce\NextIdeHelper\Tests\Fixtures\SomeMixin;
use Soyhuce\NextIdeHelper\Tests\TestCase;
use Soyhuce\NextIdeHelper\Tests\UsesFixtures;

class FactoriesCommandTest extends TestCase
{
    use UsesFixtures;

    /**
     * @test
     */
    public function the_command_is_successful()
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
    }
}
