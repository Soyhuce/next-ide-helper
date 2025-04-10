<?php declare(strict_types=1);

namespace Soyhuce\NextIdeHelper\Domain\Models\Actions;

use ReflectionClass;
use ReflectionMethod;
use Soyhuce\NextIdeHelper\Contracts\ModelResolver;
use Soyhuce\NextIdeHelper\Domain\Models\Entities\Model;
use Soyhuce\NextIdeHelper\Entities\Method;
use Soyhuce\NextIdeHelper\Support\Reflection\FunctionReflection;
use function array_slice;

class ResolveModelScopesFromAttribute implements ModelResolver
{
    public function execute(Model $model): void
    {
        $methods = $this->findScopeMethods($model);

        /** @var ReflectionMethod $method */
        foreach ($methods as $method) {
            $scope = Method::new($method->getName())
                ->source(FunctionReflection::source($method))
                ->line(FunctionReflection::line($method));

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
        return collect((new ReflectionClass($model->fqcn))->getMethods(ReflectionMethod::IS_PROTECTED))
            ->filter(fn (ReflectionMethod $method): bool => !$method->isStatic())
            ->filter(fn (ReflectionMethod $method): bool => $this->isScope($method))
            ->all();
    }

    private function isScope(ReflectionMethod $method): bool
    {
        foreach ($method->getAttributes() as $attribute) {
            // using string instead of class constant to avoid failing on older Laravel versions
            if ($attribute->getName() === 'Illuminate\\Database\\Eloquent\\Attributes\\Scope') {
                return true;
            }
        }

        return false;
    }
}
