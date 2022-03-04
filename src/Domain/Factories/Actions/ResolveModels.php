<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Factories\Actions;

use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Factories\Entities\Factory;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\ModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;

class ResolveModels
{
    public function execute(Collection $factories): ModelCollection
    {
        $models = new ModelCollection();

        /** @var Factory $factory */
        foreach ($factories as $factory) {
            $model = new Model($factory->instance()->modelName(), '');
            $factory->model = $model;
            $models->add($model);
        }

        return $models;
    }
}
