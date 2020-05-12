<?php

namespace Soyhuce\NextIdeHelper\Tests;

trait UsesFixtures
{
    protected function fixturePath(?string $path = null): string
    {
        return realpath(__DIR__ . '/Fixtures/' . $path);
    }

    protected function expectedPath(?string $path = null): string
    {
        return realpath(__DIR__ . '/expected/' . $path);
    }
}
