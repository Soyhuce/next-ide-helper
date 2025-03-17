<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Meta;

use Illuminate\Support\Str;
use function is_string;

class MetaCallable
{
    public function __construct(
        public string|array $method,
        public int $argumentIndex = 0,
    ) {}

    public function toFunction(): string
    {
        if (is_string($this->method)) {
            return Str::start($this->method, '\\');
        }

        return Str::start(implode('::', $this->method), '\\');
    }
}
