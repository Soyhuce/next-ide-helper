<?php

namespace Soyhuce\NextIdeHelper\Tests;

use Soyhuce\NextIdeHelper\NextIdeHelperServiceProvider;
use function in_array;

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

    protected function onlyForPhp81(): void
    {
        if (version_compare(PHP_VERSION, '8.1', '<')) {
            $this->markTestSkipped('Test skipped for php < 8.1');
        }
    }
}
