<?php

namespace Soyhuce\NextIdeHelper\Domain\Macros\Output;

use ReflectionClass;
use ReflectionFunction;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;
use Soyhuce\NextIdeHelper\Entities\PendingMethod;

class MacrosHelperFile
{
    private ReflectionClass $class;

    public function __construct(ReflectionClass $class)
    {
        $this->class = $class;
    }

    public function amend(IdeHelperFile $ideHelperFile)
    {
        $helperClass = $ideHelperFile->getOrAddClass($this->class->getName());

        foreach ($this->macros() as $name => $macro) {
            $macro = new ReflectionFunction($macro);

            $helperClass->addMethod(PendingMethod::new($name)->from($macro));
        }
    }

    private function macros(): array
    {
        $property = $this->class->getProperty('macros');
        $property->setAccessible(true);

        return $property->getValue();
    }
}
