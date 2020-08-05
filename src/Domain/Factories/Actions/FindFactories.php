<?php

namespace Soyhuce\NextIdeHelper\Domain\Factories\Actions;

use Composer\Autoload\ClassMapGenerator;
use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;
use Illuminate\Support\Collection;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Domain\Factories\Entities\Factory;
use Soyhuce\NextIdeHelper\Exceptions\DirectoryDoesNotExist;

class FindFactories
{
    public function execute(string $dirPath): Collection
    {
        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            throw new DirectoryDoesNotExist($dirPath);
        }

        $factories = new Collection();

        foreach (ClassMapGenerator::createMap($dirPath) as $class => $path) {
            if ($this->isEloquentFactory($class)) {
                $factories->add(new Factory($class, realpath($path)));
            }
        }

        return $factories->sortBy(fn (Factory $factory) => $factory->fqcn);
    }

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
