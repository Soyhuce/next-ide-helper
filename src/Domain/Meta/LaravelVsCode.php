<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta;

use Error;
use Illuminate\Support\Str;
use Throwable;

/**
 * Extracted from laravel/vs-code-extension.
 *
 * @see https://github.com/laravel/vs-code-extension/blob/main/php-templates/translations.php
 */
class LaravelVsCode
{
    public static function relativePath(string $path)
    {
        if (!str_contains($path, base_path())) {
            return $path;
        }

        return Str::ltrim(str_replace(base_path(), '', realpath($path) ?: $path), DIRECTORY_SEPARATOR);
    }

    public static function isVendor(string $path)
    {
        return str_contains($path, base_path('vendor'));
    }

    public static function outputMarker($key)
    {
        return '__VSCODE_LARAVEL_' . $key . '__';
    }

    public static function startupError(Throwable $e): void
    {
        throw new Error(self::outputMarker('STARTUP_ERROR') . ': ' . $e->getMessage());
    }
}
