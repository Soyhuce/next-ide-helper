<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Contracts;

use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;

interface Amender
{
    public function amend(IdeHelperFile $file): void;
}
