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

    public function amend(IdeHelperFile $ideHelperFile): void
    {
        $class = $ideHelperFile->getOrAddClass($this->class->getName());

        foreach ($this->macros() as $name => $macro) {
            $macro = new ReflectionFunction($macro);

            $class->addMethod(Method::fromFunction($name, $macro));
        }

        $constructor = $this->constructor($this->class);
        if ($constructor !== null) {
            $class->addMethod($constructor);
        }
    }

    /**
     * @return array<string, callable>
     */
    private function macros(): array
    {
        $property = $this->class->getProperty('macros');
        $property->setAccessible(true);

        return $property->getValue();
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
