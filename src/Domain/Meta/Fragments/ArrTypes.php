<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Fragments;

use Illuminate\Support\Arr;
use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
use Soyhuce\NextIdeHelper\Domain\Meta\MetaCallable;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

class ArrTypes implements MetaFragment
{
    public function add(PhpstormMetaFile $metaFile): void
    {
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'add'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'except'], 0));
        $metaFile->addOverrideElementType(new MetaCallable([Arr::class, 'first'], 0));
        $metaFile->addOverrideElementType(new MetaCallable([Arr::class, 'last'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'take'], 0));
        $metaFile->addOverrideElementType(new MetaCallable([Arr::class, 'get'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'only'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'prepend'], 0));
        $metaFile->addOverrideElementType(new MetaCallable([Arr::class, 'pull'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'set'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'shuffle'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'sort'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'sortDesc'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'sortRecursive'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'sortRecursiveDesc'], 0));
        $metaFile->addOverrideType(new MetaCallable([Arr::class, 'where'], 0));
    }
}
