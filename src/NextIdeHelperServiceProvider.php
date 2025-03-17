<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper;

use Soyhuce\NextIdeHelper\Console\AliasesCommand;
use Soyhuce\NextIdeHelper\Console\AllCommand;
use Soyhuce\NextIdeHelper\Console\FactoriesCommand;
use Soyhuce\NextIdeHelper\Console\MacrosCommand;
use Soyhuce\NextIdeHelper\Console\MetaCommand;
use Soyhuce\NextIdeHelper\Console\ModelsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NextIdeHelperServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('next-ide-helper')
            ->hasConfigFile()
            ->hasCommands(
                AliasesCommand::class,
                AllCommand::class,
                FactoriesCommand::class,
                MacrosCommand::class,
                MetaCommand::class,
                ModelsCommand::class,
            );
    }
}
