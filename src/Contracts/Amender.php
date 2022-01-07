<?php

namespace Soyhuce\NextIdeHelper\Contracts;

use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;

interface Amender
{
    public function amend(IdeHelperFile $file): void;
}
