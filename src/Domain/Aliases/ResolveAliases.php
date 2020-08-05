<?php

namespace Soyhuce\NextIdeHelper\Domain\Aliases;

use Illuminate\Foundation\AliasLoader;

class ResolveAliases
{
    /**
     * @return array<string, string>
     */
    public function execute(): array
    {
        return AliasLoader::getInstance()->getAliases();
    }
}
