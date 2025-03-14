<?php declare(strict_types=1);

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Uri;

afterEach(function (): void {
    File::delete($this->fixturePath('_ide_aliases.php'));
});

test('the command is successful', function (): void {
    if (config('app.aliases.Concurrency') === null) {
        AliasLoader::getInstance(['Concurrency' => Concurrency::class]);
    }
    if (config('app.aliases.Uri') === null) {
        AliasLoader::getInstance(['Uri' => Uri::class]);
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
});
