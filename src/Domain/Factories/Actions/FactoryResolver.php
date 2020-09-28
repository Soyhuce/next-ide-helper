<?php

namespace Soyhuce\NextIdeHelper\Domain\Factories\Actions;

use Soyhuce\NextIdeHelper\Domain\Factories\Entities\Factory;

interface FactoryResolver
{
    public function execute(Factory $factory): void;
}
