<?php

namespace Soyhuce\NextIdeHelper\Entities;

use ReflectionFunction;
use Soyhuce\NextIdeHelper\Support\Reflection\FunctionReflection;

class PendingMethod
{
    public string $name;

    public ?string $params = null;

    public ?string $return = null;

    public ?string $body = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function new(string $name): self
    {
        return new static($name);
    }

    public function params(?string $params): self
    {
        $this->params = $params;

        return $this;
    }

    public function returns(?string $return): self
    {
        $this->return = $return;

        return $this;
    }

    public function body(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function from(ReflectionFunction $function): self
    {
        $this->params(FunctionReflection::parameters($function))
            ->returns(FunctionReflection::returnType($function))
            ->body(FunctionReflection::bodyLines($function));

        return $this;
    }
}
