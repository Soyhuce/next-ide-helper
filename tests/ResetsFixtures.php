<?php

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

        $this->beforeApplicationDestroyed(function () {
            $this->restoreFixtures();
        });
    }

    public function backupFixtures(): void
    {
        $this->fixtureBackup = Collection::make(File::allFiles($this->fixturePath()))
            ->mapWithKeys(function (string $file) {
                return [$file => File::get($file)];
            });
    }

    public function restoreFixtures(): void
    {
        $this->fixtureBackup->each(static function (string $content, string $file) {
            File::put($file, $content);
        });
    }
}
