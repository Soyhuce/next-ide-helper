<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support\Output;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use function dirname;

class PhpstormMetaFile
{
    private string $filePath;

    /** @var Collection<int, string> */
    private Collection $lines;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->lines = new Collection();
    }

    public function addOverrideType(string $function, int $argumentIndex): void
    {
        $this->lines->push("override({$function}(0), type({$argumentIndex}));");
    }

    public function addOverrideElementType(string $function, int $argumentIndex): void
    {
        $this->lines->push("override({$function}(0), elementType({$argumentIndex}));");
    }

    /**
     * @param Collection<string, string> $map
     */
    public function addOverrideMap(string $function, Collection $map): void
    {
        $this->lines->push(
            "override({$function}(), map([",
        );
        $map->each(function (string $value, string $key): void {
            $this->lines->push("    {$key} => {$value},");
        });
        $this->lines->push(
            ']));',
        );
    }

    public function render(): void
    {
        $content = $this->lines
            ->map(fn (string $line) => Str::rtrim('    ' . $line, ' '))
            ->implode(PHP_EOL);

        if (!File::isDirectory(dirname($this->filePath))) {
            File::makeDirectory(dirname($this->filePath), recursive: true);
        }
        File::put(
            $this->filePath,
            <<<PHP
            <?php
            
            namespace PHPSTORM_META {
            
            {$content}
            
            }
            PHP
        );
    }
}
