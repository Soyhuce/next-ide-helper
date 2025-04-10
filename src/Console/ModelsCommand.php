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
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelScopesFromAttribute;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\ModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Domain\Models\Printers\ModelDocBlockPrinter;
use Soyhuce\NextIdeHelper\Domain\Models\Printers\ModelMixinPrinter;
use Soyhuce\NextIdeHelper\Domain\Models\Printers\ModelPrinter;

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
        $models = $models->sortBy(fn (Model $model) => $model->fqcn)->values();

        foreach ($this->resolvers($models) as $resolver) {
            foreach ($models as $model) {
                $resolver->execute($model);
            }
        }

        $this->printer()->print($models);
    }

    /**
     * @return array<ModelResolver>
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
                new ResolveModelScopesFromAttribute(),
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

    /**
     * @return array<int, class-string<ModelResolver>>
     */
    private function modelExtensions(): array
    {
        return config('next-ide-helper.models.extensions', []);
    }

    private function printer(): ModelPrinter
    {
        return config('next-ide-helper.models.use_mixin', false)
            ? new ModelMixinPrinter()
            : new ModelDocBlockPrinter();
    }
}
