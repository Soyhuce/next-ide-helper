<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Soyhuce\NextIdeHelper\Domain\Meta\Actions\ResolveContainerBindings;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

class MetaCommand extends Command
{
    use BootstrapsApplication;

    /** @var string */
    protected $signature = 'next-ide-helper:meta';

    /** @var string */
    protected $description = 'Generate an IDE helper file to help phpstorm understand some Laravel magic';

    /** @var array<string> */
    protected array $methods = [
        '\\Illuminate\\Container\\Container::makeWith',
        '\\Illuminate\\Contracts\\Container\\Container::make',
        '\\Illuminate\\Contracts\\Container\\Container::makeWith',
        '\\Illuminate\\Support\\Facades\\App::make',
        '\\Illuminate\\Support\\Facades\\App::makeWith',
        '\\app',
        '\\resolve',
    ];

    public function handle(ResolveContainerBindings $resolveContainerBindings): void
    {
        $this->bootstrapApplication();

        $bindings = $resolveContainerBindings->execute()
            ->prepend(value: '@', key: '')
            ->mapWithKeys(fn (string $value, string $key) => ["'{$key}'" => "'{$value}'"]);

        $metaFile = new PhpstormMetaFile(config('next-ide-helper.meta.file_name'));

        foreach ($this->methods as $method) {
            $metaFile->addOverrideMap($method, $bindings);
        }

        $this->addArr($metaFile);
        $this->addHelpers($metaFile);

        $metaFile->render();
    }

    private function addArr(PhpstormMetaFile $metaFile): void
    {
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::add', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::except', 0);
        $metaFile->addOverrideElementType('\\Illuminate\\Support\\Arr::first', 0);
        $metaFile->addOverrideElementType('\\Illuminate\\Support\\Arr::last', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::take', 0);
        $metaFile->addOverrideElementType('\\Illuminate\\Support\\Arr::get', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::only', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::prepend', 0);
        $metaFile->addOverrideElementType('\\Illuminate\\Support\\Arr::pull', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::set', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::shuffle', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::sort', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::sortDesc', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::sortRecursive', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::sortRecursiveDesc', 0);
        $metaFile->addOverrideType('\\Illuminate\\Support\\Arr::where', 0);
    }

    private function addHelpers(PhpstormMetaFile $metaFile): void
    {
        $metaFile->addOverrideElementType('\\head', 0);
        $metaFile->addOverrideElementType('\\last',0);
    }
}
