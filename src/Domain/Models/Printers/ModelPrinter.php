<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Printers;

use Soyhuce\NextIdeHelper\Domain\Models\Collections\ModelCollection;

interface ModelPrinter
{
    public function print(ModelCollection $models): void;
}
