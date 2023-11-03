<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

trait ResetsFixtures
{
    use UsesFixtures;

    private Collection $fixtureBackup;

    public function bootResetsFixtures(): void
    {
        $this->backupFixtures();

        $this->beforeApplicationDestroyed(function (): void {
            $this->restoreFixtures();
        });
    }

    public function backupFixtures(): void
    {
        $this->fixtureBackup = Collection::make(File::allFiles($this->fixturePath()))
            ->mapWithKeys(static fn (string $file) => [$file => File::get($file)]);
    }

    public function restoreFixtures(): void
    {
        $this->fixtureBackup->each(static function (string $content, string $file): void {
            File::put($file, $content);
        });
    }
}
