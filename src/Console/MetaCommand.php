<?php

namespace Soyhuce\NextIdeHelper\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Soyhuce\NextIdeHelper\Domain\Meta\Actions\ResolveContainerBindings;

class MetaCommand extends Command
{
    use BootstrapsApplication;

    /** @var string */
    protected $signature = 'next-ide-helper:meta';

    /** @var string */
    protected $description = 'Generate an IDE helper file to help phpstorm understand some Laravel magic';

    /** @var array<string> */
    protected array $methods = [
        '\Illuminate\Container\Container::makeWith',
        '\Illuminate\Contracts\Container\Container::make',
        '\Illuminate\Contracts\Container\Container::makeWith',
        '\App::make',
        '\App::makeWith',
        '\app',
        '\resolve',
    ];

    public function handle(ResolveContainerBindings $resolveContainerBindings): void
    {
        $this->bootstrapApplication();

        $view = view('next-ide-helper::meta', [
            'methods' => $this->methods,
            'bindings' => $resolveContainerBindings->execute(),
        ]);

        File::put(config('next-ide-helper.meta.file_name'), $view->render());
    }
}
