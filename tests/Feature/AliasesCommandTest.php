<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests\Feature;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\File;
use Soyhuce\NextIdeHelper\Tests\ResetsFixtures;
use Soyhuce\NextIdeHelper\Tests\TestCase;

/**
 * @coversNothing
 */
class AliasesCommandTest extends TestCase
{
    use ResetsFixtures;

    /**
     * @test
     */
    public function theCommandIsSuccessful(): void
    {
        if (config('app.aliases.Concurrency') === null) {
            AliasLoader::getInstance(['Concurrency' => \Illuminate\Support\Facades\Concurrency::class]);
        }
        if (config('app.aliases.Uri') === null) {
            AliasLoader::getInstance(['Uri' => \Illuminate\Support\Uri::class]);
        }

        config([
            'next-ide-helper.aliases' => [
                'file_name' => $this->fixturePath() . '/_ide_aliases.php',
            ],
        ]);

        $this->artisan('next-ide-helper:aliases')
            ->assertExitCode(0);

        $this->assertFileEquals(
            $this->expectedPath('_ide_aliases.stub'),
            $this->fixturePath('_ide_aliases.php')
        );
    }

    protected function tearDown(): void
    {
        File::delete($this->fixturePath('_ide_aliases.php'));
        parent::tearDown();
    }
}
