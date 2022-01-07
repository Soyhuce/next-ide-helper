<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\Reflection\FunctionReflection;
use function array_slice;

class ResolveModelScopes implements ModelResolver
{
    public function execute(Model $model): void
    {
        $methods = $this->findScopeMethods($model);

        /** @var ReflectionMethod $method */
        foreach ($methods as $method) {
            $scope = Method::new($this->methodName($method->getName()));

            $parameters = array_slice(FunctionReflection::parameterList($method), 1);
            $scope->parameters(implode(', ', $parameters));

            $model->addScope($scope);
        }
    }

    /**
     * @return array<ReflectionMethod>
     */
    private function findScopeMethods(Model $model): array
    {
        return collect((new ReflectionClass($model->fqcn))->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(fn (ReflectionMethod $method): bool => !$method->isStatic())
            ->filter(fn (ReflectionMethod $method): bool => $this->isScope($method->getName()))
            ->all();
    }

    private function isScope(string $name): bool
    {
        return Str::startsWith($name, 'scope');
    }

    private function methodName(string $name): string
    {
        return Str::of($name)->after('scope')->camel();
    }
}
