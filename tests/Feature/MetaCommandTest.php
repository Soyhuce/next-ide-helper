<?php

namespace Soyhuce\NextIdeHelper\Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Tests\Fixtures\SomeMacroable;
use Soyhuce\NextIdeHelper\Tests\Fixtures\SomeMixin;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversDefaultClass \Soyhuce\NextIdeHelper\Console\MetaCommand
 */
class MetaCommandTest extends TestCase
{
    /**
     * @test
     * @covers ::handle
     */
    public function the_command_is_successful()
    {
        config([
            'next-ide-helper.meta' => [
                'file_name' => $this->fixturePath() . '/.phpstorm.meta.php',
            ],
        ]);

        $this->artisan('next-ide-helper:meta')
            ->assertExitCode(0);
    }

    protected function tearDown(): void
    {
        File::delete($this->fixturePath('.phpstorm.meta.php'));
        parent::tearDown();
    }
}
