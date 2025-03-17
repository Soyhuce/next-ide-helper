<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Fragments;

use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

class HelperTypes implements MetaFragment
{
    public function add(PhpstormMetaFile $metaFile): void
    {
        $metaFile->addOverrideElementType('\\head', 0);
        $metaFile->addOverrideElementType('\\last', 0);
    }
}
