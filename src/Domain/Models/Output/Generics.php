<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Output;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class Generics
{
    public static function isEnabled(): bool
    {
        return Config::boolean('next-ide-helper.models.larastan_friendly', false);
    }

    public static function get(string $class, string $type): string
    {
        if (!self::isEnabled()) {
            return $class;
        }

        if (!Str::startsWith($type, '<')) {
            $type = "<{$type}>";
        }

        return $class . $type;
    }
}
