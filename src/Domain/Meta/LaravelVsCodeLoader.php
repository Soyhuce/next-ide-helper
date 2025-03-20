<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta;

use Illuminate\Support\Collection;

class LaravelVsCodeLoader
{
    /**
     * @return Collection<array-key, mixed>
     */
    public static function load(string $file): Collection
    {
        if (!class_exists('LaravelVsCode')) {
            class_alias(LaravelVsCode::class, 'LaravelVsCode');
        }

        ob_start();

        require __DIR__ . "/../../../php-templates/{$file}.php";

        $output = ob_get_clean();

        return new Collection(json_decode($output, associative: true));
    }
}
