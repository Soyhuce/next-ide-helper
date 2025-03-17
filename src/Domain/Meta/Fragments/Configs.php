<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Fragments;

use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
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
            new MetaCallable([Repository::class, 'get'], 0),
            new MetaCallable([Repository::class, 'set'], 0),
            new MetaCallable([Config::class, 'get'], 0),
            new MetaCallable([Config::class, 'set'], 0),
            new MetaCallable('config', 0),
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function resolveConfigs(): Collection
    {
        return (new Collection(config()->all()))
            ->dot()
            ->keys()
            ->flatMap(fn (string $key) => $this->explodeKey($key))
            ->sort()
            ->unique()
            ->values();
    }

    /**
     * @return array<int, string>
     */
    private function explodeKey(string $key, string $prefix = ''): array
    {
        if ($prefix === $key) {
            return [];
        }

        $segment = (string) Str::of($key)->after($prefix)->ltrim('.')->before('.');
        if (is_numeric($segment)) {
            return [];
        }

        $nextPrefix = Str::ltrim($prefix . '.' . $segment, '.');

        return [
            $nextPrefix,
            ...$this->explodeKey($key, $nextPrefix),
        ];
    }
}
