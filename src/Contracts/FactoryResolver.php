<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Contracts;

use Soyhuce\NextIdeHelper\Domain\Factories\Entities\Factory;

interface FactoryResolver
{
    public function execute(Factory $factory): void;
}
