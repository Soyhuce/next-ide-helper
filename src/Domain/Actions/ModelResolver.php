<?php

namespace Soyhuce\NextIdeHelper\Domain\Actions;

use Soyhuce\NextIdeHelper\Domain\Models\Model;

interface ModelResolver
{
    public function execute(Model $model): void;
}
