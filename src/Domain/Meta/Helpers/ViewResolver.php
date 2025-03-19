<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta\Helpers;

use Illuminate\Support\Facades\Blade;
use Symfony\Component\Finder\Finder;

/**
 * Extracted from laravel/vs-code-extension.
 *
 * @see https://github.com/laravel/vs-code-extension/blob/main/php-templates/views.php
 */
class ViewResolver
{
    public function getAllViews()
    {
        $finder = app('view')->getFinder();

        $paths = collect($finder->getPaths())->flatMap(fn ($path) => $this->findViews($path));

        $hints = collect($finder->getHints())->flatMap(
            fn ($paths, $key) => collect($paths)->flatMap(
                fn ($path) => collect($this->findViews($path))->map(
                    fn ($value) => array_merge($value, ['key' => "{$key}::{$value['key']}"])
                )
            )
        );

        [$local, $vendor] = $paths
            ->merge($hints)
            ->values()
            ->partition(fn ($v) => !$v['isVendor']);

        return $local
            ->sortBy('key', SORT_NATURAL)
            ->merge($vendor->sortBy('key', SORT_NATURAL));
    }

    public function getAllComponents()
    {
        if (!is_file(base_path('vendor/composer/autoload_psr4.php'))) {
            return [];
        }

        $namespaced = Blade::getClassComponentNamespaces();
        $autoloaded = require base_path('vendor/composer/autoload_psr4.php');
        $components = [];

        foreach ($namespaced as $key => $ns) {
            $path = null;

            foreach ($autoloaded as $namespace => $paths) {
                if (str_starts_with($ns, $namespace)) {
                    foreach ($paths as $p) {
                        $test = str($ns)->replace($namespace, '')->replace('\\', '/')->prepend($p . DIRECTORY_SEPARATOR)->toString();

                        if (is_dir($test)) {
                            $path = $test;

                            break;
                        }
                    }

                    break;
                }
            }

            if (!$path) {
                continue;
            }

            $files = Finder::create()
                ->files()
                ->name('*.php')
                ->in($path);

            foreach ($files as $file) {
                $realPath = $file->getRealPath();

                $components[] = [
                    'path' => str_replace(base_path(DIRECTORY_SEPARATOR), '', $realPath),
                    'isVendor' => str_contains($realPath, base_path('vendor')),
                    'key' => str($realPath)
                        ->replace(realpath($path), '')
                        ->replace('.php', '')
                        ->ltrim(DIRECTORY_SEPARATOR)
                        ->replace(DIRECTORY_SEPARATOR, '.')
                        ->kebab()
                        ->prepend($key . '::'),
                ];
            }
        }

        return $components;
    }

    protected function findViews($path)
    {
        $paths = [];

        if (!is_dir($path)) {
            return $paths;
        }

        $files = Finder::create()
            ->files()
            ->name('*.blade.php')
            ->in($path);

        foreach ($files as $file) {
            $paths[] = [
                'path' => str_replace(base_path(DIRECTORY_SEPARATOR), '', $file->getRealPath()),
                'isVendor' => str_contains($file->getRealPath(), base_path('vendor')),
                'key' => (string) str($file->getRealPath())
                    ->replace(realpath($path), '')
                    ->replace('.blade.php', '')
                    ->ltrim(DIRECTORY_SEPARATOR)
                    ->replace(DIRECTORY_SEPARATOR, '.'),
            ];
        }

        return $paths;
    }
}
