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
        if (config('app.aliases.Redis') === null) {
            AliasLoader::getInstance(['RedisManager' => \Illuminate\Support\Facades\Redis::class]);
        }
        if (config('app.aliases.Number') === null) {
            AliasLoader::getInstance(['Number' => \Illuminate\Support\Number::class]);
        }
        if (config('app.aliases.Schedule') === null) {
            AliasLoader::getInstance(['Schedule' => \Illuminate\Support\Facades\Schedule::class]);
        }
        if (config('app.aliases.Context') === null) {
            AliasLoader::getInstance(['Context' => \Illuminate\Support\Facades\Context::class]);
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
