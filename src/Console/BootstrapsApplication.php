<?php

namespace Soyhuce\NextIdeHelper\Console;

trait BootstrapsApplication
{
    private function bootstrapApplication()
    {
        $bootstrapper = config('next-ide-helper.bootstrapper');

        if ($bootstrapper === null) {
            return;
        }

        app($bootstrapper)->bootstrap();
    }
}
