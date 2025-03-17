<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Support\Output;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Domain\Meta\MetaCallable;
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

    public function addOverrideType(MetaCallable $callable): void
    {
        $this->lines->push("override({$callable->toFunction()}(0), type({$callable->argumentIndex}));");
    }

    public function addOverrideElementType(MetaCallable $callable): void
    {
        $this->lines->push("override({$callable->toFunction()}(0), elementType({$callable->argumentIndex}));");
    }

    /**
     * @param Collection<string, string> $map
     */
    public function addOverrideMap(MetaCallable $callable, Collection $map): void
    {
        $this->lines->push(
            "override({$callable->toFunction()}(), map([",
        );
        $map->each(function (string $value, string $key): void {
            $this->lines->push("    {$key} => {$value},");
        });
        $this->lines->push(
            ']));',
        );
    }

    public function addSimpleOverride(MetaCallable $callable, string $type): void
    {
        $this->lines->push(
            "override({$callable->toFunction()}(), map(['' => {$type}]));",
        );
    }

    /**
     * @param Collection<int, mixed> $allowedValues
     */
    public function registerArgumentSet(string $argumentSet, Collection $allowedValues): void
    {
        $this->lines->push(
            "registerArgumentsSet('{$argumentSet}',"
        );
        foreach ($allowedValues as $allowedValue) {
            $value = var_export($allowedValue, true);
            $this->lines->push("    {$value},");
        }
        $this->lines->push(');');
    }

    public function expectedArgumentsFromSet(MetaCallable $callable, string $argumentSet): void
    {
        $this->lines->push(
            "expectedArguments({$callable->toFunction()}(), {$callable->argumentIndex}, argumentsSet('{$argumentSet}'));"
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
