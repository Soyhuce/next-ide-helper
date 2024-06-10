<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ApplyAttributeOverrides;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\FindModels;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributes;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelAttributesFromAttributes;
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
                if ($this->isModelExcluded($model)) {
                    continue;
                }

                $resolver->execute($model);
            }
        }

        foreach ($models as $model) {
            if ($this->isModelExcluded($model)) {
                continue;
            }

            foreach ($this->renderers($model) as $renderer) {
                $renderer->render();
            }
            foreach ($this->amenders($model) as $amender) {
                $amender->amend($ideHelperFile);
            }
        }

        $ideHelperFile->render();
    }

    /**
     * @return array<\Soyhuce\NextIdeHelper\Contracts\ModelResolver>
     */
    private function resolvers(ModelCollection $models): array
    {
        return array_merge(
            [
                new ResolveModelAttributes(),
                new ResolveModelAttributesFromGetters(),
                new ResolveModelAttributesFromAttributes(),
                new ResolveModelAttributesFromCasts(),
                new ResolveModelCollection(),
                new ResolveModelQueryBuilder(),
                new ResolveModelScopes(),
                new ResolveModelRelations($models),
            ],
            collect($this->modelExtensions())
                ->map(static fn (string $class): ModelResolver => new $class())
                ->toArray(),
            [
                new ApplyAttributeOverrides(config('next-ide-helper.models.overrides', [])),
            ]
        );
    }

    private function isModelExcluded(Model $model): bool
    {
        return in_array(get_class($model->instance()), config('next-ide-helper.models.excludes', []), true);
    }

    /**
     * @return array<int, class-string<\Soyhuce\NextIdeHelper\Contracts\ModelResolver>>
     */
    private function modelExtensions(): array
    {
        return config('next-ide-helper.models.extensions', []);
    }

    /**
     * @return array<\Soyhuce\NextIdeHelper\Contracts\Renderer>
     */
    private function renderers(Model $model): array
    {
        return [
            new ModelDocBlock($model),
            new QueryBuilderDocBlock($model),
        ];
    }

    /**
     * @return array<\Soyhuce\NextIdeHelper\Contracts\Amender>
     */
    private function amenders(Model $model): array
    {
        return [
            new QueryBuilderHelperFile($model),
            new RelationsHelperFile($model),
        ];
    }
}
