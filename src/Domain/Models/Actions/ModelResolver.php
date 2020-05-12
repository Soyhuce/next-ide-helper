<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

interface ModelResolver
{
    public function execute(Model $model): void;
}
