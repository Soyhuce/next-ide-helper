<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests;

use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Orchestra\Testbench\TestCase as Orchestra;
use Soyhuce\NextIdeHelper\NextIdeHelperServiceProvider;
use function in_array;

abstract class TestCase extends Orchestra
{
    use InteractsWithDeprecationHandling;
    use UsesFixtures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutDeprecationHandling();

        $this->loadMigrationsFrom(__DIR__ . '/database');

        if (in_array(ResetsFixtures::class, class_uses_recursive($this))) {
            $this->bootResetsFixtures();
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            NextIdeHelperServiceProvider::class,
        ];
    }
}
