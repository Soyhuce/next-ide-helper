<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Actions;

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Throwable;
use function is_object;

class ResolveContainerBindings
{
    public function __construct(
        private Application $application,
    ) {}

    public function execute(): Collection
    {
        return collect($this->application->getBindings())
            ->keys()
            ->mapWithKeys(fn (string $abstract) => [$abstract => $this->resolve($abstract)])
            ->filter()
            ->filter(static fn ($concrete) => is_object($concrete))
            ->map(static fn ($concrete) => $concrete::class)
            ->sortKeys();
    }

    /**
     * @return mixed|null
     */
    private function resolve(string $abstract)
    {
        try {
            return $this->application->get($abstract);
        } catch (Throwable) {
            return;
        }
    }
}
