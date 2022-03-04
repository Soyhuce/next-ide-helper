<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use ReflectionClass;
use Soyhuce\ClassMapGenerator\ClassMapGenerator;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\ModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Exceptions\DirectoryDoesNotExist;

class FindModels
{
    public function execute(string $dirPath): ModelCollection
    {
        if (!is_dir($dirPath)) {
            throw new DirectoryDoesNotExist($dirPath);
        }

        $models = new ModelCollection();

        foreach (ClassMapGenerator::createMap($dirPath) as $class => $path) {
            $path = realpath($path);

            if ($this->isEloquentModel($class) && $path !== false) {
                $models->add(new Model($class, $path));
            }
        }

        return $models;
    }

    /**
     * @param class-string $class
     */
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
