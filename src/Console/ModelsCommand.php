<?php

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\FindModels;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributes;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributesFromCasts;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributesFromGetters;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelQueryBuilder;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelRelations;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelScopes;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\ModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Output\ModelDocBlock;
use Soyhuce\NextIdeHelper\Domain\Models\Output\QueryBuilderDocBlock;
use Soyhuce\NextIdeHelper\Domain\Models\Output\QueryBuilderHelperFile;
use Soyhuce\NextIdeHelper\Domain\Models\Output\RelationsHelperFile;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;

class ModelsCommand extends Command
{
    use BootstrapsApplication;

    /** @var string */
    protected $name = 'next-ide-helper:models';

    /** @var string */
    protected $description = 'Generates meta-data to help ide understand your models.';

    public function handle(FindModels $findModels): void
    {
        $this->bootstrapApplication();

        $models = new ModelCollection();
        foreach (config('next-ide-helper.models.directories') as $directory) {
            $models = $models->merge($findModels->execute($directory));
        }

        $ideHelperFile = new IdeHelperFile(config('next-ide-helper.models.file_name'));

        foreach ($this->resolvers($models) as $resolver) {
            foreach ($models as $model) {
                $resolver->execute($model);
            }
        }

        foreach ($models as $model) {
            foreach ($this->renderers($model) as $renderer) {
                $renderer->render();
            }
            foreach ($this->amenders($model) as $amender) {
                $amender->amend($ideHelperFile);
            }
        }

        $ideHelperFile->render();
    }

    private function resolvers(ModelCollection $models): array
    {
        return array_merge(
            [
                new ResolveModelAttributes(),
                new ResolveModelAttributesFromGetters(),
                new ResolveModelAttributesFromCasts(),
                new ResolveModelCollection(),
                new ResolveModelQueryBuilder(),
                new ResolveModelScopes(),
                new ResolveModelRelations($models),
            ],
            Collection::make(config('next-ide-helper.models.extensions'))
                ->map(static fn(string $class): ModelResolver => new $class())
                ->toArray()
        );
    }

    private function renderers(Model $model): array
    {
        return [
            new ModelDocBlock($model),
            new QueryBuilderDocBlock($model),
        ];
    }

    private function amenders(Model $model): array
    {
        return [
            new QueryBuilderHelperFile($model),
            new RelationsHelperFile($model),
        ];
    }
}
