<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Fragments;

use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

class ArrTypes implements MetaFragment
{
    public function add(PhpstormMetaFile $metaFile): void
    {
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::add', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::except', 0);
        $metaFile->addOverrideElementType('\\Illuminate\\Support\\Arr::first', 0);
        $metaFile->addOverrideElementType('\\Illuminate\\Support\\Arr::last', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::take', 0);
        $metaFile->addOverrideElementType('\\Illuminate\\Support\\Arr::get', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::only', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::prepend', 0);
        $metaFile->addOverrideElementType('\\Illuminate\\Support\\Arr::pull', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::set', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::shuffle', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::sort', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::sortDesc', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::sortRecursive', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::sortRecursiveDesc', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::where', 0);
    }
}
