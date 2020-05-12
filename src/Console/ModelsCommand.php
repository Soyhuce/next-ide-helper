<?php

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Actions\FindModels;
use Soyhuce\NextIdeHelper\Domain\Actions\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelAttributes;
use Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelAttributesFromGetters;
use Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelCollection;
use Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelQueryBuilder;
use Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelRelations;
use Soyhuce\NextIdeHelper\Domain\Actions\ResolveModelScopes;
use Soyhuce\NextIdeHelper\Domain\Collections\ModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Model;
use Soyhuce\NextIdeHelper\Domain\Output\HelperFile\IdeHelperFile;
use Soyhuce\NextIdeHelper\Domain\Output\ModelDocBlock;
use Soyhuce\NextIdeHelper\Domain\Output\QueryBuilderDocBlock;
use Soyhuce\NextIdeHelper\Domain\Output\QueryBuilderHelperFile;
use Soyhuce\NextIdeHelper\Domain\Output\RelationsHelperFile;

class ModelsCommand extends Command
{
    use BootstrapsApplication;

    /** @var string */
    protected $name = 'next-ide-helper:models';

    /** @var string */
    protected $description = 'Generates meta-data to help ide understand your models.';

    public function handle(): void
    {
        $this->bootstrapApplication();

        $models = new ModelCollection();
        $findModels = new FindModels();
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
                new ResolveModelCollection(),
                new ResolveModelQueryBuilder(),
                new ResolveModelScopes(),
                new ResolveModelRelations($models),
            ],
            Collection::make(config('next-ide-helper.models.extensions'))
                ->map(static fn (string $class): ModelResolver => new $class())
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
