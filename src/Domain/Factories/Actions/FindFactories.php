<?php

namespace Soyhuce\NextIdeHelper\Domain\Factories\Actions;

use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Collection;
use ReflectionClass;
use Soyhuce\ClassmapGenerator\ClassmapGenerator;
use Soyhuce\NextIdeHelper\Domain\Factories\Entities\Factory;
use Soyhuce\NextIdeHelper\Exceptions\DirectoryDoesNotExist;

class FindFactories
{
    public function execute(string $dirPath): Collection
    {
        if (!is_dir($dirPath)) {
            throw new DirectoryDoesNotExist($dirPath);
        }

        $factories = new Collection();

        foreach (ClassmapGenerator::createMap($dirPath) as $class => $path) {
            $path = realpath($path);

            if ($this->isEloquentFactory($class) && $path !== false) {
                $factories->add(new Factory($class, $path));
            }
        }

        return $factories->sortBy(fn (Factory $factory) => $factory->fqcn);
    }

    /**
     * @param class-string $class
     */
    private function isEloquentFactory(string $class): bool
    {
        $reflexion = new ReflectionClass($class);

        if (!$reflexion->isSubclassOf(EloquentFactory::class)) {
            return false;
        }

        if ($reflexion->isAbstract()) {
            return false;
        }

        return true;
    }
}
