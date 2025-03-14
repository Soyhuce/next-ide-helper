<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests;

trait UsesFixtures
{
    protected function fixturePath(?string $path = null): string
    {
        return __DIR__ . '/Fixtures/' . $path;
    }

    protected function fixtureFile(string $path): string
    {
        return file_get_contents($this->fixturePath($path));
    }
}
