<?php

namespace Soyhuce\NextIdeHelper\Domain\Meta\Actions;

use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Throwable;

class ResolveContainerBindings
{
    private Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function execute(): Collection
    {
        return collect($this->application->getBindings())
            ->keys()
            ->mapWithKeys(fn (string $abstract) => [$abstract => $this->resolve($abstract)])
            ->filter()
            ->filter(static fn ($concrete) => is_object($concrete))
            ->map(static fn ($concrete) => get_class($concrete))
            ->sortKeys();
    }

    /**
     * @param string $abstract
     * @return mixed|null
     */
    private function resolve(string $abstract)
    {
        try {
            return $this->application->get($abstract);
        } catch (Throwable $throwable) {
            return;
        }
    }
}
