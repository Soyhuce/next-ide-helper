<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Fragments;

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;
use Throwable;
use function is_object;

class ContainerBindings implements MetaFragment
{
    /** @var array<string> */
    protected array $methods = [
        '\\Illuminate\\Container\\Container::makeWith',
        '\\Illuminate\\Contracts\\Container\\Container::make',
        '\\Illuminate\\Contracts\\Container\\Container::makeWith',
        '\\Illuminate\\Support\\Facades\\App::make',
        '\\Illuminate\\Support\\Facades\\App::makeWith',
        '\\app',
        '\\resolve',
    ];

    public function __construct(
        private Application $application,
    ) {}

    public function add(PhpstormMetaFile $metaFile): void
    {
        $bindings = $this->resolveBindings()
            ->prepend(value: '@', key: '')
            ->mapWithKeys(fn (string $value, string $key) => ["'{$key}'" => "'{$value}'"]);

        foreach ($this->methods as $method) {
            $metaFile->addOverrideMap($method, $bindings);
        }
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
