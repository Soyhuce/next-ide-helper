<?php

namespace Soyhuce\NextIdeHelper\Domain\Macros\Actions;

use Composer\Autoload\ClassMapGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Soyhuce\NextIdeHelper\Exceptions\DirectoryDoesNotExist;

class FindMacroableClasses
{
    public function execute(string $dirPath): Collection
    {
        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            throw new DirectoryDoesNotExist($dirPath);
        }

        $macroables = new Collection();

        foreach (ClassMapGenerator::createMap($dirPath) as $class => $path) {
            if ($this->isMacroable($class)) {
                $macroables->add($class);
            }
        }

        return $macroables;
    }

    private function isMacroable(string $class): bool
    {
        if (!class_exists($class, false)) {
            return false;
        }

        if (!in_array(Macroable::class, class_uses_recursive($class))) {
            return false;
        }

        $reflectionClass = new \ReflectionClass($class);

        if (!$reflectionClass->hasProperty('macros')) {
            return false;
        }

        $property = $reflectionClass->getProperty('macros');
        $property->setAccessible(true);

        if (empty($property->getValue())) {
            return false;
        }

        return true;
    }
}
