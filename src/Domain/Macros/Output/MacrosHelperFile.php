<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Macros\Output;

use Carbon\FactoryImmutable;
use Carbon\Traits\Macro as CarbonMacro;
use Closure;
use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Illuminate\Support\Traits\Macroable as IlluminateMacroable;
use ReflectionClass;
use ReflectionFunction;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;
use function in_array;

class MacrosHelperFile
{
    public function __construct(
        private ReflectionClass $class,
        private ?string $facade,
    ) {}

    public function amend(IdeHelperFile $ideHelperFile): void
    {
        $class = $ideHelperFile->getOrAddClass($this->class->getName());
        $facade = $this->facade !== null ? $ideHelperFile->getOrAddClass($this->facade) : null;

        foreach ($this->macros() as $name => $macro) {
            $macro = new ReflectionFunction($macro);

            $method = Method::fromFunction($name, $macro);
            $class->addDocTag($method->toDocTag());

            $facade?->addDocTag($method->isStatic(true)->toDocTag());

            $link = $method->toLinkTag();
            if ($link !== null) {
                $class->addDocTag($link);
                $facade?->addDocTag($link);
            }
        }

        $constructor = $this->constructor($this->class);
        if ($constructor !== null) {
            $class->addMethod($constructor);
        }
    }

    /**
     * @return array<string, Closure>
     */
    private function macros(): array
    {
        if (in_array(IlluminateMacroable::class, class_uses_recursive($this->class->getName()), true)) {
            return $this->illuminateMacros();
        }

        if (in_array(CarbonMacro::class, class_uses_recursive($this->class->getName()), true)) {
            return $this->carbonMacros();
        }

        return [];
    }

    /**
     * @return array<string, Closure>
     */
    private function illuminateMacros(): array
    {
        return $this->class->getProperty('macros')->getValue();
    }

    /**
     * @return array<string, Closure>
     */
    private function carbonMacros(): array
    {
        if (InstalledVersions::satisfies(new VersionParser(), 'nesbot/carbon', '^2.0')) {
            return $this->class->getProperty('globalMacros')->getValue();
        }

        return FactoryImmutable::getDefaultInstance()->getSettings()['macros'];
    }

    private function constructor(ReflectionClass $class): ?Method
    {
        $constructor = $class->getConstructor();
        if ($constructor === null) {
            return null;
        }

        return Method::fromMethod('__construct', $constructor);
    }
}
