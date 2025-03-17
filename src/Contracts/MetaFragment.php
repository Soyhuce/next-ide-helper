<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Contracts;

use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

interface MetaFragment
{
    public function add(PhpstormMetaFile $metaFile): void;
}
