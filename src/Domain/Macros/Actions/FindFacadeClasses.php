<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Macros\Actions;

use Composer\ClassMapGenerator\ClassMapGenerator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Exceptions\DirectoryDoesNotExist;

class FindFacadeClasses
{
    /**
     * @return Collection<int, class-string<Facade>>
     */
    public function execute(string $dirPath): Collection
    {
        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            throw new DirectoryDoesNotExist($dirPath);
        }

        $facades = new Collection();

        foreach (ClassMapGenerator::createMap($dirPath) as $class => $path) {
            if ($this->isFacade($class)) {
                $facades->add($class);
            }
        }

        return $facades;
    }

    private function isFacade(string $class): bool
    {
        if (!class_exists($class, false)) {
            return false;
        }

        $reflectionClass = new ReflectionClass($class);

        return $reflectionClass->isSubclassOf(Facade::class)
            && !$reflectionClass->isAbstract();
    }
}
