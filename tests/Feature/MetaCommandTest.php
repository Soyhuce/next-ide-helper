<?php

namespace Soyhuce\NextIdeHelper\Tests\Feature;

use Illuminate\Support\Facades\File;
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
    public function theCommandIsSuccessful(): void
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
