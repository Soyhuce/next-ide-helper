<?php

namespace Soyhuce\NextIdeHelper\Domain\Macros\Output;

use ReflectionClass;
use ReflectionFunction;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;

class MacrosHelperFile
{
    private ReflectionClass $class;

    public function __construct(ReflectionClass $class)
    {
        $this->class = $class;
    }

    public function amend(IdeHelperFile $ideHelperFile)
    {
        $class = $ideHelperFile->getOrAddClass($this->class->getName());

        foreach ($this->macros() as $name => $macro) {
            $macro = new ReflectionFunction($macro);

            $class->addMethod(Method::fromFunction($name, $macro));
        }
    }

    private function macros(): array
    {
        $property = $this->class->getProperty('macros');
        $property->setAccessible(true);

        return $property->getValue();
    }
}
