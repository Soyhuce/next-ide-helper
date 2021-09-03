<?php

namespace Soyhuce\NextIdeHelper\Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Tests\Fixtures\SomeMacroable;
use Soyhuce\NextIdeHelper\Tests\Fixtures\SomeMixin;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversNothing
 */
class MacrosCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        SomeMacroable::macro('foo', static function (string $bar): string {
            return Str::upper($bar);
        });

        SomeMacroable::mixin(new SomeMixin());
    }

    /**
     * @test
     */
    public function theCommandIsSuccessful(): void
    {
        config([
            'next-ide-helper.macros' => [
                'directories' => [$this->fixturePath()],
                'file_name' => $this->fixturePath() . '/_ide_macros.php',
            ],
        ]);

        $this->artisan('next-ide-helper:macros')
            ->assertExitCode(0);

        $this->assertFileEquals(
            $this->expectedPath('_ide_macros.stub'),
            $this->fixturePath('_ide_macros.php')
        );
    }

    protected function tearDown(): void
    {
        File::delete($this->fixturePath('_ide_macros.php'));
        parent::tearDown();
    }
}
