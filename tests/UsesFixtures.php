<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests;

trait UsesFixtures
{
    protected function fixturePath(?string $path = null): string
    {
        return realpath(__DIR__ . '/Fixtures/' . $path);
    }

    protected function fixture80Path(?string $path = null): string
    {
        return realpath(__DIR__ . '/Fixtures80/' . $path);
    }

    protected function fixture81Path(?string $path = null): string
    {
        return realpath(__DIR__ . '/Fixtures81/' . $path);
    }

    protected function expectedPath(?string $path = null): string
    {
        return realpath(__DIR__ . '/expected/' . $path);
    }
}
