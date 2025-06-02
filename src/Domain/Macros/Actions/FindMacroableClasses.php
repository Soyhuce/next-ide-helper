<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Macros\Actions;

use Carbon\FactoryImmutable;
use Carbon\Traits\Macro as CarbonMacro;
use Composer\ClassMapGenerator\ClassMapGenerator;
use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable as IlluminateMacroable;
use ReflectionClass;
use Soyhuce\NextIdeHelper\Exceptions\DirectoryDoesNotExist;
use function in_array;

class FindMacroableClasses
{
    public function execute(string $dirPath): Collection
    {
        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            throw new DirectoryDoesNotExist($dirPath);
        }

        $macroables = new Collection();

        foreach (ClassMapGenerator::createMap($dirPath) as $class => $path) {
            if ($this->isMacroable($class)) {
                $macroables->add($class);
            }
        }

        return $macroables;
    }

    /**
     * @param class-string $class
     */
    private function isMacroable(string $class): bool
    {
        if (!class_exists($class, false)) {
            return false;
        }

        return $this->isIlluminateMacroable($class)
            || $this->isCarbonMacro($class);
    }

    /**
     * @param class-string $class
     */
    private function isIlluminateMacroable(string $class): bool
    {
        if (!in_array(IlluminateMacroable::class, class_uses_recursive($class), true)) {
            return false;
        }

        $reflectionClass = new ReflectionClass($class);

        if (!$reflectionClass->hasProperty('macros')) {
            return false;
        }

        $property = $reflectionClass->getProperty('macros');

        if (empty($property->getValue())) {
            return false;
        }

        return true;
    }

    /**
     * @param class-string $class
     */
    private function isCarbonMacro(string $class): bool
    {
        if (!in_array(CarbonMacro::class, class_uses_recursive($class), true)) {
            return false;
        }

        if (InstalledVersions::satisfies(new VersionParser(), 'nesbot/carbon', '^2.0')) {
            $reflectionClass = new ReflectionClass($class);

            if (!$reflectionClass->hasProperty('globalMacros')) {
                return false;
            }

            $property = $reflectionClass->getProperty('globalMacros');

            if (empty($property->getValue())) {
                return false;
            }

            return true;
        }

        $macros = FactoryImmutable::getDefaultInstance()->getSettings()['macros'] ?? [];

        if (empty($macros)) {
            return false;
        }

        return true;
    }
}
