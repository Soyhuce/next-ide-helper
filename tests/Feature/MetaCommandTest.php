<?php declare(strict_types=1);

use Illuminate\Support\Facades\File;

afterEach(function (): void {
    File::delete($this->fixturePath('.phpstorm.meta.php'));
});

test('the command is successful', function (): void {
    config([
        'next-ide-helper.meta' => [
            'file_name' => $this->fixturePath() . '/.phpstorm.meta.php',
        ],
    ]);

    $this->app->setBasePath(realpath(__DIR__ . '/../../'));

    $this->artisan('next-ide-helper:meta')
        ->assertExitCode(0);
});
