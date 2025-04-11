<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Domain\Macros\Actions\FindFacadeClasses;
use Soyhuce\NextIdeHelper\Domain\Macros\Actions\FindMacroableClasses;
use Soyhuce\NextIdeHelper\Domain\Macros\Output\MacrosHelperFile;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;
use Throwable;

class MacrosCommand extends Command
{
    use BootstrapsApplication;

    /** @var string */
    protected $signature = 'next-ide-helper:macros';

    /** @var string */
    protected $description = 'Generate an IDE helper file for Laravel macros';

    public function handle(
        FindMacroableClasses $findMacroables,
        FindFacadeClasses $findFacades,
    ): void {
        $this->bootstrapApplication();

        $macroables = new Collection();
        $facades = new Collection();
        foreach (config('next-ide-helper.macros.directories') as $directory) {
            $macroables = $macroables->merge($findMacroables->execute($directory));
            $facades = $facades->merge($findFacades->execute($directory));
        }

        $ideHelperFile = new IdeHelperFile(config('next-ide-helper.macros.file_name'));

        $facades = $facades->mapWithKeys($this->resolveFacadeRoot(...));

        foreach ($macroables as $macroable) {
            $amender = new MacrosHelperFile(new ReflectionClass($macroable), $facades->get($macroable));
            $amender->amend($ideHelperFile);
        }

        $ideHelperFile->render();
    }

    /**
     * @param class-string<Facade> $facade
     * @return array<class-string, class-string<Facade>>
     */
    public function resolveFacadeRoot(string $facade): array
    {
        try {
            return [$facade::getFacadeRoot()::class => $facade];
        } catch (Throwable) {
            return [];
        }
    }
}
