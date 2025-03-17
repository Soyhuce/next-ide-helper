<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Soyhuce\NextIdeHelper\Contracts\MetaFragment;
use Soyhuce\NextIdeHelper\Domain\Meta\Fragments\ArrTypes;
use Soyhuce\NextIdeHelper\Domain\Meta\Fragments\Configs;
use Soyhuce\NextIdeHelper\Domain\Meta\Fragments\ContainerBindings;
use Soyhuce\NextIdeHelper\Domain\Meta\Fragments\HelperTypes;
use Soyhuce\NextIdeHelper\Domain\Meta\Fragments\RouteNames;
use Soyhuce\NextIdeHelper\Support\Output\PhpstormMetaFile;

class MetaCommand extends Command
{
    use BootstrapsApplication;

    /** @var string */
    protected $signature = 'next-ide-helper:meta';

    /** @var string */
    protected $description = 'Generate an IDE helper file to help phpstorm understand some Laravel magic';

    public function handle(): void
    {
        $this->bootstrapApplication();

        $metaFile = new PhpstormMetaFile(config('next-ide-helper.meta.file_name'));

        foreach ($this->fragments() as $fragment) {
            app($fragment)->add($metaFile);
        }

        $metaFile->render();
    }

    /**
     * @return array<int, class-string<MetaFragment>>
     */
    private function fragments(): array
    {
        return [
            ContainerBindings::class,
            ArrTypes::class,
            HelperTypes::class,
            Configs::class,
            RouteNames::class,
        ];
    }
}
