<?php

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\UsesReflection;

class ResolveModelScopes implements ModelResolver
{
    use UsesReflection;

    public function execute(Model $model): void
    {
        $methods = $this->findScopeMethods($model);

        /** @var ReflectionMethod $method */
        foreach ($methods as $method) {
            $scope = new Method(
                $this->methodName($method->getName()),
                $this->methodParameters($method->getParameters())
            );

            $model->addScope($scope);
        }
    }

    /**
     * @return array<\ReflectionMethod>
     */
    private function findScopeMethods(Model $model): array
    {
        return collect((new ReflectionClass($model->fqcn))->getMethods(ReflectionMethod::IS_PUBLIC))
            ->filter(fn (ReflectionMethod $method): bool => !$method->isStatic())
            ->filter(fn (ReflectionMethod $method): bool => $this->isScope($method->getName()))
            ->all();
    }

    private function isScope(string $name): string
    {
        return Str::startsWith($name, 'scope');
    }

    private function methodName(string $name): string
    {
        return Str::of($name)->after('scope')->camel();
    }

    /**
     * @param array<\ReflectionParameter> $reflectionParameters
     * @return array<string>
     */
    private function methodParameters(array $reflectionParameters): array
    {
        $parameters = [];

        for ($i = 1; $i < count($reflectionParameters); $i++) {
            $parameters[] = $this->parameterString($reflectionParameters[$i]);
        }

        return $parameters;
    }
}
