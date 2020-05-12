<?php

namespace Soyhuce\NextIdeHelper\Domain\Actions;

use Composer\Autoload\ClassMapGenerator;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Domain\Collections\ModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Model;
use Soyhuce\NextIdeHelper\Exceptions\DirectoryDoesNotExist;

class FindModels
{
    public function execute(string $dirPath): ModelCollection
    {
        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            throw new DirectoryDoesNotExist($dirPath);
        }

        $models = new ModelCollection();

        foreach (ClassMapGenerator::createMap($dirPath) as $class => $path) {
            if ($this->isEloquentModel($class)) {
                $models->add(new Model($class, realpath($path)));
            }
        }

        return $models;
    }

    private function isEloquentModel(string $class): bool
    {
        $reflexion = new ReflectionClass($class);

        if (!$reflexion->isSubclassOf(EloquentModel::class)) {
            return false;
        }

        if ($reflexion->isAbstract()) {
            return false;
        }

        return true;
    }
}
