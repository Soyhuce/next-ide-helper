<?php

namespace Soyhuce\NextIdeHelper\Tests;

use Soyhuce\NextIdeHelper\NextIdeHelperServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use UsesFixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database');

        if (in_array(ResetsFixtures::class, class_uses_recursive($this))) {
            $this->bootResetsFixtures();
        }
    }

    protected function getPackageProviders($app)
    {
        return [NextIdeHelperServiceProvider::class];
    }
}
