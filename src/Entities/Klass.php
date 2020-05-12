<?php

namespace Soyhuce\NextIdeHelper\Entities;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Soyhuce\NextIdeHelper\Entities\PendingMethod;

class Klass
{
    private string $name;

    private ?string $extends = null;

    private Collection $docTags;

    private Collection $methods;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->docTags = new Collection();
        $this->methods = new Collection();
    }

    public function extends(string $class): self
    {
        $this->extends = $class;

        return $this;
    }

    public function addDocTags(Collection $docTags): self
    {
        $this->docTags = $this->docTags->merge($docTags);

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addMethod(PendingMethod $method)
    {
        $result = "public function {$method->name}(";
        if ($method->params !== null) {
            $result .= $method->params;
        }
        $result .= ')';

        if ($method->return !== null) {
            $result .= ": {$method->return}";
        }

        if ($method->body !== null) {
            $result .= PHP_EOL . '{' . PHP_EOL . $method->body . PHP_EOL . '}';
        } else {
            $result .= ' {}';
        }

        $this->methods->add($result);
    }

    public function toString(): string
    {
        $result = PHP_EOL;

        if ($this->docTags->isNotEmpty()) {
            $result .= $this->docTags
                ->prepend('/**')
                ->push(' */')
                ->map(static fn (string $tag): string => "    ${tag}")
                ->implode(PHP_EOL) . PHP_EOL;
        }

        $result .= "    class {$this->name}";

        if ($this->extends !== null) {
            $result .= ' extends ' . Str::start($this->extends, '\\');
        }

        $result .= ' {';

        if ($this->methods->isNotEmpty()) {
            $result .= PHP_EOL .
                $this->methods->sort()
                    ->map(static fn (string $function) => '        ' . $function)
                    ->implode(PHP_EOL . PHP_EOL)
                . PHP_EOL;
            $result .= '    }';
        } else {
            $result .= '}';
        }

        return $result;
    }
}
