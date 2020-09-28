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

        if (!$this->eloquentFactoryExist()) {
            $this->info('It looks like you are not using Laravel 8 factories');
            $this->info('If you are under Laravel 7, check https://github.com/Soyhuce/laravel-8-factories');

            return;
        }

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

    private function eloquentFactoryExist(): bool
    {
        return class_exists(\Illuminate\Database\Eloquent\Factories\Factory::class);
    }

    /**
     * @return array<\Soyhuce\NextIdeHelper\Domain\Models\Actions\ModelResolver>
     */
    private function modelResolver(ModelCollection $models): array
    {
        return [
            new ResolveModelCollection(),
            new ResolveModelRelations($models),
        ];
    }

    /**
     * @return array<\Soyhuce\NextIdeHelper\Domain\Factories\Actions\FactoryResolver>
     */
    private function factoryResolvers(): array
    {
        return collect(config('next-ide-helper.factories.extensions'))
            ->map(fn (string $class) => new $class())
            ->toArray();
    }
}
