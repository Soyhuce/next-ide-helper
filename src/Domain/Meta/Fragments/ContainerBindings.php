<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Fragments;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
use Soyhuce\NextIdeHelper\Domain\Meta\LaravelVsCodeLoader;
use Soyhuce\NextIdeHelper\Domain\Meta\MetaCallable;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;
use Throwable;
use function is_object;

class ContainerBindings implements MetaFragment
{
    public function __construct(
        private Application $application,
    ) {}

    public function add(PhpstormMetaFile $metaFile): void
    {
        $bindings = $this->resolveBindings()
            ->prepend(value: '@', key: '')
            ->mapWithKeys(fn (string $value, string $key) => ["'{$key}'" => "'{$value}'"]);

        foreach ($this->methods() as $method) {
            $metaFile->addOverrideMap($method, $bindings);
        }
    }

    /**
     * @return array<int, MetaCallable>
     */
    public function methods(): array
    {
        return [
            new MetaCallable([Container::class, 'makeWith']),
            new MetaCallable([ContainerContract::class, 'make']),
            new MetaCallable([ContainerContract::class, 'makeWith']),
            new MetaCallable([App::class, 'make']),
            new MetaCallable([App::class, 'makeWith']),
            new MetaCallable([Application::class, 'make']),
            new MetaCallable([Application::class, 'makeWith']),
            new MetaCallable('app'),
            new MetaCallable('resolve'),
        ];
    }

    /**
     * @return Collection<string, string>
     */
    public function resolveBindings(): Collection
    {
        return collect($this->application->getBindings())
            ->keys()
            ->mapWithKeys(fn (string $abstract) => [$abstract => $this->resolve($abstract)])
            ->filter()
            ->filter(static fn ($concrete) => is_object($concrete))
            ->map(static fn ($concrete) => $concrete::class)
            ->sortKeys();
    }

    private function resolve(string $abstract): mixed
    {
        try {
            return $this->application->get($abstract);
        } catch (Throwable) {
            return null;
        }
    }
}
