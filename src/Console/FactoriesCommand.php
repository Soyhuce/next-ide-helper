<?php

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Domain\Factories\Actions\FindFactories;
use Soyhuce\NextIdeHelper\Domain\Factories\Actions\ResolveModels;
use Soyhuce\NextIdeHelper\Domain\Factories\Output\FactoryDocBlock;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelCollection;
use Soyhuce\NextIdeHelper\Domain\Models\Actions\ResolveModelRelations;
use Soyhuce\NextIdeHelper\Domain\Models\Collections\ModelCollection;

class FactoriesCommand extends Command
{
    use BootstrapsApplication;

    /** @var string */
    protected $signature = 'next-ide-helper:factories';

    /** @var string */
    protected $description = 'Add docblocks to model factories';

    public function handle(FindFactories $findFactories, ResolveModels $resolveModels): void
    {
        $this->bootstrapApplication();

        $factories = new Collection();
        foreach (config('next-ide-helper.factories.directories') as $directory) {
            $factories = $factories->merge($findFactories->execute($directory));
        }

        $models = $resolveModels->execute($factories);

        foreach ($this->modelResolver($models) as $resolver) {
            foreach ($factories as $factory) {
                $resolver->execute($factory->model);
            }
        }

        foreach ($this->factoryResolvers() as $factoryResolver) {
            foreach ($factories as $factory) {
                $factoryResolver->execute($factory);
            }
        }

        foreach ($factories as $factory) {
            (new FactoryDocBlock($factory))->render();
        }
    }

    /**
     * @return array<\Soyhuce\NextIdeHelper\Contracts\ModelResolver>
     */
    private function modelResolver(ModelCollection $models): array
    {
        return [
            new ResolveModelCollection(),
            new ResolveModelRelations($models),
        ];
    }

    /**
     * @return array<\Soyhuce\NextIdeHelper\Contracts\FactoryResolver>
     */
    private function factoryResolvers(): array
    {
        return collect($this->factoryExtensions())
            ->map(fn (string $class) => new $class())
            ->toArray();
    }

    /**
     * @return array<int, class-string<\Soyhuce\NextIdeHelper\Contracts\FactoryResolver>>
     */
    private function factoryExtensions(): array
    {
        return config('next-ide-helper.factories.extensions', []);
    }
}
