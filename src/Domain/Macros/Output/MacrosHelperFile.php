<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Macros\Output;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\Output\IdeHelperFile;

class MacrosHelperFile
{
    public function __construct(
        private ReflectionClass $class,
    ) {}

    public function amend(IdeHelperFile $ideHelperFile): void
    {
        $class = $ideHelperFile->getOrAddClass($this->class->getName());

        foreach ($this->macros() as $name => $macro) {
            $macro = new ReflectionFunction($macro);

            $method = Method::fromFunction($name, $macro);
            $class->addDocTag($method->toDocTag());

            $link = $method->toLinkTag();
            if ($link !== null) {
                $class->addDocTag($link);
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
