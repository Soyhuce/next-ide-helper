<?php declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Macroable\SomeFacade;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Macroable\SomeMacroable;
use Soyhuce\NextIdeHelper\Tests\Fixtures\Macroable\SomeMixin;

beforeEach(function (): void {
    SomeMacroable::macro('foo', static fn (string $bar): string => Str::upper($bar));

    SomeMacroable::mixin(new SomeMixin());

    SomeFacade::macro('testFacade', fn (): string => 'foo');
});

afterEach(function (): void {
    File::delete($this->fixturePath('_ide_macros.php'));
});

test('the command is successful', function (): void {
    config([
        'next-ide-helper.macros' => [
            'directories' => [$this->fixturePath('Macroable')],
            'file_name' => $this->fixturePath() . '/_ide_macros.php',
        ],
    ]);

    $this->artisan('next-ide-helper:macros')
        ->assertExitCode(0);

    expect($this->fixtureFile('_ide_macros.php'))->toMatchSnapshot();
});
