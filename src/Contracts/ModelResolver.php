<?php

namespace Soyhuce\NextIdeHelper\Contracts;

use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

interface ModelResolver
{
    public function execute(Model $model): void;
}
