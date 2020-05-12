<?php

namespace Soyhuce\NextIdeHelper\Domain\Output;

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
