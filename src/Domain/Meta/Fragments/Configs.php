<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Fragments;

use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
use Soyhuce\NextIdeHelper\Domain\Meta\LaravelVsCodeLoader;
use Soyhuce\NextIdeHelper\Domain\Meta\MetaCallable;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

class Configs implements MetaFragment
{
    public function add(PhpstormMetaFile $metaFile): void
    {
        $configs = $this->resolveConfigs();

        $metaFile->registerArgumentSet('configs', $configs);

        foreach ($this->methods() as $method) {
            $metaFile->expectedArgumentsFromSet($method, 'configs');
        }
    }

    /**
     * @return array<int, MetaCallable>
     */
    private function methods(): array
    {
        return [
            new MetaCallable([Repository::class, 'has'], 0),
            new MetaCallable([Repository::class, 'get'], 0),
            new MetaCallable([Repository::class, 'string'], 0),
            new MetaCallable([Repository::class, 'integer'], 0),
            new MetaCallable([Repository::class, 'float'], 0),
            new MetaCallable([Repository::class, 'boolean'], 0),
            new MetaCallable([Repository::class, 'array'], 0),
            new MetaCallable([Repository::class, 'set'], 0),
            new MetaCallable([Repository::class, 'prepend'], 0),
            new MetaCallable([Repository::class, 'push'], 0),
            new MetaCallable([Config::class, 'has'], 0),
            new MetaCallable([Config::class, 'get'], 0),
            new MetaCallable([Config::class, 'string'], 0),
            new MetaCallable([Config::class, 'integer'], 0),
            new MetaCallable([Config::class, 'float'], 0),
            new MetaCallable([Config::class, 'boolean'], 0),
            new MetaCallable([Config::class, 'array'], 0),
            new MetaCallable([Config::class, 'set'], 0),
            new MetaCallable([Config::class, 'prepend'], 0),
            new MetaCallable([Config::class, 'push'], 0),
            new MetaCallable('config', 0),
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function resolveConfigs(): Collection
    {
        return LaravelVsCodeLoader::load('configs')
            ->map(fn (array $config) => $config['name'])
            ->sort()
            ->values();
    }
}
