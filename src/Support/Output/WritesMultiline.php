<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support\Output;

trait WritesMultiline
{
    protected function line(?string $line): string
    {
        if ($line === null || $line === '') {
            return '';
        }

        return $line . PHP_EOL;
    }
}
