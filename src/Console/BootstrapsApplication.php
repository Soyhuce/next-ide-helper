<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Console;

trait BootstrapsApplication
{
    private function bootstrapApplication(): void
    {
        $bootstrapper = config('next-ide-helper.bootstrapper');

        if ($bootstrapper === null) {
            return;
        }

        app($bootstrapper)->bootstrap();
    }
}
