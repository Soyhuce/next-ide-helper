<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Fragments;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\View\Factory;
use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
use Soyhuce\NextIdeHelper\Domain\Meta\LaravelVsCodeLoader;
use Soyhuce\NextIdeHelper\Domain\Meta\MetaCallable;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

class Views implements MetaFragment
{
    public function add(PhpstormMetaFile $metaFile): void
    {
        $views = $this->resolveViews();

        $metaFile->registerArgumentSet('views', $views);

        foreach ($this->methods() as $method) {
            $metaFile->expectedArgumentsFromSet($method, 'views');
        }
    }

    /**
     * @return array<int, MetaCallable>
     */
    private function methods(): array
    {
        return [
            new MetaCallable([View::class, 'make'], 0),
            new MetaCallable([View::class, 'exists'], 0),
            new MetaCallable([Factory::class, 'make'], 0),
            new MetaCallable([Factory::class, 'exists'], 0),
            new MetaCallable('view', 0),
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function resolveViews(): Collection
    {
        $anonymousComponentPaths = Collection::make(app('blade.compiler')->getAnonymousComponentPaths())
            ->pluck('prefixHash');

        return LaravelVsCodeLoader::load('views')
            ->map(fn (array $view) => $view['key'])
            ->filter(function (string $view) use ($anonymousComponentPaths) {
                if (!Str::contains($view, '::')) {
                    return true;
                }

                $namespace = Str::before($view, '::');

                return $anonymousComponentPaths->doesntContain($namespace);
            })
            ->sort()
            ->values();
    }
}
