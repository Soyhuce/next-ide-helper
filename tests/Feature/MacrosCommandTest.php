<?php declare(strict_types=1);

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Macroable\SomeFacade;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Macroable\SomeMacroable;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Macroable\SomeMixin;

beforeEach(function (): void {
    SomeMacroable::macro('foo', static fn (string $bar): string => Str::upper($bar));

    SomeMacroable::mixin(new SomeMixin());

    SomeFacade::macro('testFacade', fn (): string => 'foo');

    Date::macro('testDateMacro', fn (): string => 'bar');
});

afterEach(function (): void {
    File::delete($this->fixturePath('_ide_macros.php'));
});

test('the command is successful', function (): void {
    config([
        'next-ide-helper.macros' => [
            'directories' => [
                $this->fixturePath('Macroable'),
                __DIR__ . '/../../vendor/laravel/framework/src/Illuminate/Support',
            ],
            'file_name' => $this->fixturePath() . '/_ide_macros.php',
        ],
    ]);

    $this->artisan('next-ide-helper:macros')
        ->assertExitCode(0);

    expect($this->fixtureFile('_ide_macros.php'))->toMatchSnapshot();
})->skip(!InstalledVersions::satisfies(new VersionParser(), 'nesbot/carbon', '^3.0'));

test('the command is successful with carbon 2', function (): void {
    config([
        'next-ide-helper.macros' => [
            'directories' => [
                $this->fixturePath('Macroable'),
                __DIR__ . '/../../vendor/laravel/framework/src/Illuminate/Support',
            ],
            'file_name' => $this->fixturePath() . '/_ide_macros.php',
        ],
    ]);

    $this->artisan('next-ide-helper:macros')
        ->assertExitCode(0);

    expect($this->fixtureFile('_ide_macros.php'))->toMatchSnapshot();
})->skip(!InstalledVersions::satisfies(new VersionParser(), 'nesbot/carbon', '^2.0'));
