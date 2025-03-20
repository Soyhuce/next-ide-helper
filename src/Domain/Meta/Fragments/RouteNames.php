<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Fragments;

use Illuminate\Routing\Redirector;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
use Soyhuce\NextIdeHelper\Domain\Meta\LaravelVsCodeLoader;
use Soyhuce\NextIdeHelper\Domain\Meta\MetaCallable;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

class RouteNames implements MetaFragment
{
    public function add(PhpstormMetaFile $metaFile): void
    {
        $routeNames = $this->resolveRouteNames();

        $metaFile->registerArgumentSet('routes', $routeNames);

        foreach ($this->methods() as $method) {
            $metaFile->expectedArgumentsFromSet($method, 'routes');
        }
    }

    /**
     * @return array<int, MetaCallable>
     */
    private function methods(): array
    {
        return [
            new MetaCallable([Redirector::class, 'route'], 0),
            new MetaCallable([URL::class, 'signedRoute'], 0),
            new MetaCallable([URL::class, 'temporarySignedRoute'], 0),
            new MetaCallable([RouteCollection::class, 'getByName'], 0),
            new MetaCallable([RouteCollectionInterface::class, 'getByName'], 0),
            new MetaCallable([Router::class, 'respondWithRoute'], 0),
            new MetaCallable([Route::class, 'respondWithRoute'], 0),
            new MetaCallable('route', 0),
            new MetaCallable('to_route', 0),
        ];
    }

    /**
     * @return Collection<int, string>
     */
    private function resolveRouteNames(): Collection
    {
        return LaravelVsCodeLoader::load('routes')
            ->map(fn (array $route) => $route['name'])
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }
}
