<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Tests;

use ErrorException;
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

        if (in_array(ResetsFixtures::class, class_uses_recursive($this), true)) {
            $this->bootResetsFixtures();
        }

        $this->app->setBasePath(realpath(__DIR__));
    }

    protected function getPackageProviders($app)
    {
        return [
            NextIdeHelperServiceProvider::class,
        ];
    }

    protected function withoutDeprecationHandling(): static
    {
        if ($this->originalDeprecationHandler == null) {
            $this->originalDeprecationHandler = set_error_handler(function (
                $level,
                $message,
                $file = '',
                $line = 0,
            ): void {
                if (in_array($level, [E_DEPRECATED, E_USER_DEPRECATED], true) || (error_reporting() & $level)) {
                    // Silenced vendor errors
                    if (str_starts_with($file, base_path(__DIR__ . '/../vendor/symfony/'))) {
                        return;
                    }

                    if (str_starts_with($file, realpath(__DIR__ . '/../vendor/composer/class-map-generator'))) {
                        return;
                    }

                    if (str_starts_with($file, realpath(__DIR__ . '/../vendor/composer/pcre'))) {
                        return;
                    }

                    throw new ErrorException($message, 0, $level, $file, $line);
                }
            });
        }

        return $this;
    }
}
